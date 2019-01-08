<?php

namespace Vanguard\Http\Controllers\Auth;

use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Libraries\Utilities;
use Vanguard\Mail\PasswordChanger;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable;
use Auth;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;


class AuthController extends Controller
{

    /**
     * Create a new authentication controller instance.
     * @param UserRepository $users
     */
    public function __construct(UserRepository $users)
    {
        $this->middleware('guest', ['except' => ['getLogout']]);
        $this->middleware('auth', ['only' => ['getLogout']]);
        $this->middleware('registration', ['only' => ['getRegister', 'postRegister']]);
        $this->users = $users;
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $socialProviders = config('auth.social.providers');

        return view('auth.login', compact('socialProviders'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        if(empty($request->email) || empty($request->password)){
            return redirect()->back()->with('error', 'email and or password cannot be empty');
        }

        // In case that request throttling is enabled, we have to check if user can perform this request.
        // We'll key this by the username and the IP address of the client making these requests into this application.
        $throttles = settings('throttle_enabled');

        //Redirect URL that can be passed as hidden field.
        $to = $request->has('to') ? "?to=" . $request->get('to') : '';

        if ($throttles && $this->hasTooManyLoginAttempts($request)) {
            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);


        if (! Auth::validate($credentials)) {

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return redirect()->to('login' . $to)
                ->with('error', 'email and or password invalid');
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user->isUnconfirmed()) {
            return redirect()->to('login' . $to)
                ->with('error', 'Please confirm your account first');
        }

        if ($user->isBanned()) {
            return redirect()->to('login' . $to)
                ->with('error', 'Your account has been banned, please contact your administrator');
        }

        Auth::login($user, settings('remember_me') && $request->get('remember'));

        $user_identity = Utilities::checkForActivation(Auth::user()->id);

        if($user_identity === 'Unconfirmed' || $user_identity === ''){
            Auth::logout();
        }

        $username = Auth::user()->email;

        $password = bcrypt($request->password);
        $role = \DB::table('role_user')->where('user_id', Auth::user()->id)->first();
        if ($role->role_id === 3) {
            session()->forget('agency_id');
            $user_details = Utilities::switch_db('api')->select("SELECT * FROM users WHERE email = '$username' LIMIT 1");
            $user_id = $user_details[0]->id;
            $broadcaster_details = Utilities::switch_db('api')->select("SELECT * FROM broadcasters WHERE user_id = '$user_id'");
            session(['broadcaster_id' => $broadcaster_details[0]->id]);
        } elseif ($role->role_id === 4) {
            session()->forget('broadcaster_id');
            $user_details = Utilities::switch_db('api')->select("SELECT * FROM users WHERE email = '$username' LIMIT 1");
            $user_id = $user_details[0]->id;
            $agency_details = Utilities::switch_db('api')->select("SELECT * FROM agents WHERE user_id = '$user_id'");
            session(['agency_id' => $agency_details[0]->id]);
        }

        return $this->handleUserWasAuthenticated($request, $throttles, $user);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  Request $request
     * @param  bool $throttles
     * @param $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $throttles, $user)
    {
        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        $this->users->update($user->id, ['last_login' => Carbon::now()]);

        event(new LoggedIn($user));

        if ($request->has('to')) {
            return redirect()->to($request->get('to'));
        }

        return redirect()->intended();
    }

    protected function logoutAndRedirectToTokenPage(Request $request, Authenticatable $user)
    {
        Auth::logout();

        $request->session()->put('auth.2fa.id', $user->id);

        return redirect()->route('auth.token');
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  Request  $request
     * @return array
     */
    protected function getCredentials(Request $request)
    {
        // The form field for providing username or password
        // have name of "username", however, in order to support
        // logging users in with both (username and email)
        // we have to check if user has entered one or another
        $usernameOrEmail = $request->get($this->loginUsername());

        if ($this->isEmail($usernameOrEmail)) {
            return [
                'email' => $usernameOrEmail,
                'password' => $request->get('password')
            ];
        }

        return $request->only($this->loginUsername(), 'password');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogout()
    {
        event(new LoggedOut(Auth::user()));

        Auth::logout();

        \Session::flush();

        return redirect('login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function loginUsername()
    {
        return 'email';
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return app(RateLimiter::class)->tooManyAttempts(
            $request->input($this->loginUsername()).$request->ip(),
            $this->maxLoginAttempts(), $this->lockoutTime() / 60
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function incrementLoginAttempts(Request $request)
    {
        app(RateLimiter::class)->hit(
            $request->input($this->loginUsername()).$request->ip()
        );
    }

    /**
     * Determine how many retries are left for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return int
     */
    protected function retriesLeft(Request $request)
    {
        $attempts = app(RateLimiter::class)->attempts(
            $request->input($this->loginUsername()).$request->ip()
        );

        return $this->maxLoginAttempts() - $attempts + 1;
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $request->input($this->loginUsername()).$request->ip()
        );

        return redirect('login')
            ->withInput($request->only($this->loginUsername(), 'remember'))
            ->withErrors([
                $this->loginUsername() => $this->getLockoutErrorMessage($seconds),
            ]);
    }

    /**
     * Get the login lockout error message.
     *
     * @param  int  $seconds
     * @return string
     */
    protected function getLockoutErrorMessage($seconds)
    {
        return trans('auth.throttle', ['seconds' => $seconds]);
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request)
    {
        app(RateLimiter::class)->clear(
            $request->input($this->loginUsername()).$request->ip()
        );
    }

    /**
     * Get the maximum number of login attempts for delaying further attempts.
     *
     * @return int
     */
    protected function maxLoginAttempts()
    {
        return settings('throttle_attempts', 5);
    }

    /**
     * The number of seconds to delay further login attempts.
     *
     * @return int
     */
    protected function lockoutTime()
    {
        $lockout = (int) settings('throttle_lockout_time');

        if ($lockout <= 1) {
            $lockout = 1;
        }

        return 60 * $lockout;
    }

    /**
     * Validate if provided parameter is valid email.
     *
     * @param $param
     * @return bool
     */
    private function isEmail($param)
    {
        return ! \Validator::make(
            ['email' => $param],
            ['email' => 'email']
        )->fails();
    }


    public function verifyToken($token)
    {
        $user = \DB::select("SELECT * from users where confirmation_token = '$token'");
        if($user && $user[0]->status === 'Unconfirmed'){
            $update_user = \DB::update("UPDATE users set status = 'Active' where confirmation_token = '$token'");
            \Session::flash('success', 'Your email has been verified, you can now proceed to login with your credentials');
            return redirect()->route('login');
        }elseif($user && $user[0]->status === 'Active'){
            \Session::flash('info', 'You have already verified your email, please proceed to login...');
            return redirect()->route('login');
        }elseif(!$user){
            \Session::flash('error', 'Wrong activation code...');
            return redirect()->route('login');
        }
    }

    public function getForgetPassword()
    {
        return view('auth.password.forget_password');
    }

    public function processForgetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required',
        ]);

        $user_local = \DB::select("SELECT * from users where email = '$request->email'");
        $user_api = Utilities::switch_db('api')->select("SELECT * from users where email = '$request->email'");
        if($user_local && $user_api){

            $token = encrypt($user_local[0]->id);

            $send_mail = \Mail::to($user_local[0]->email)->send(new PasswordChanger($token));

            \Session::flash('success', 'Please follow the link sent to your email');
            return redirect()->back();

        }else{

            \Session::flash('error', 'Email not found on our application');
            return redirect()->back();
        }
    }

    public function getChangePassword($token)
    {
        $user_id = decrypt($token);
        $user_details_local = \DB::select("SELECT * from users where id = '$user_id'");
        $email = $user_details_local[0]->email;
        $user_details_api = Utilities::switch_db('api')->select("SELECT * from users where email = '$email'");

        return view('auth.password.change_password', compact('user_details_local', 'user_details_api'));

    }

    public function processChangePassword(Request $request, $id_local, $id_api)
    {
        $this->validate($request, [
            'password' => 'required|min:6',
            're_password' => 'required|same:password|min:6'
        ]);

        $password = bcrypt($request->password);

        $user_local_update = \DB::update("UPDATE users set password = '$password' WHERE id = '$id_local'");
        $upser_api_update = Utilities::switch_db('api')->update("UPDATE users set password = '$password' WHERE id = '$id_api'");

        if($user_local_update && $upser_api_update){
            return redirect()->route('login')->with('success', 'You have successfully changed your password, please proceed to login');
        }else{
            return redirect()->back()->withErrors('Error occurred while processing your request, please try again');
        }
    }
}
