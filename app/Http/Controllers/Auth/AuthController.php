<?php

namespace Vanguard\Http\Controllers\Auth;

use Auth;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Http\Requests\User\PasswordChangeRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Libraries\Enum\UserStatus;
use Vanguard\Mail\PasswordChanger;
use Vanguard\Models\Broadcaster;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable;
use Vanguard\User;

class AuthController extends Controller
{
    public $login_layout;
    public $forget_password;
    public $change_password;
    public $dashboard_route;
    public $email_subject;

    public function getLayouts()
    {
        $this->login_layout = 'auth.ssp.login';
        $this->forget_password = 'auth.ssp.password.forget_password';
        $this->change_password = 'auth.ssp.password.change_password';
        $this->dashboard_route = 'broadcaster.dashboard.index';
        $this->email_subject = 'Torch Password Reset';
    }

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
        $this->getLayouts();
    }

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLogin()
    {
        $socialProviders = config('auth.social.providers');
        return view($this->login_layout, compact('socialProviders'));
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postLogin(Request $request)
    {
        if (empty($request->email) || empty($request->password)) {
            return redirect()->back()->with('error', ClassMessages::EMAIL_PASSWORD_EMPTY);
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

        if (!Auth::validate($credentials)) {

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return redirect()->to(route('login') . $to)
                ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if ($user->isUnconfirmed()) {
            return redirect()->to(route('login') . $to)
                ->with('error', ClassMessages::EMAIL_CONFIRMATION);
        }

        if ($user->isBanned()) {
            return redirect()->to(route('login') . $to)
                ->with('error', ClassMessages::BANNED_ACCOUNT);
        }

        if ($user->status === 'Unconfirmed' || $user->status === '') {
            return redirect()->to(route('login') . $to);
        }

        if ($this->isRightUser($user) === false) {
            return redirect()->to(route('login'))
                ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember);

        $this->setUserSession($user);

        return $this->handleUserWasAuthenticated($request, $throttles, $user);
    }

    /**
     * This is a temporary fix to make sure that only the appropriate user for a product is able to login
     * @todo Remove and use different user models and guards instead
     */
    protected function isRightUser($user)
    {
        return $user->company_type == CompanyTypeName::BROADCASTER;
    }

    /**
     * This is a temporary fix to set session variables
     * @todo No need to do this anymore, but need to make sure the different sessions are not used
     */
    protected function setUserSession($user)
    {
        session()->forget('agency_id');
        session(['broadcaster_id' => $user->companies->first()->id]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\Response
     */

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

        return redirect()->intended(route($this->dashboard_route));
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
                'password' => $request->get('password'),
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
            $request->input($this->loginUsername()) . $request->ip(),
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
            $request->input($this->loginUsername()) . $request->ip()
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
            $request->input($this->loginUsername()) . $request->ip()
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
            $request->input($this->loginUsername()) . $request->ip()
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
            $request->input($this->loginUsername()) . $request->ip()
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
        return !\Validator::make(
            ['email' => $param],
            ['email' => 'email']
        )->fails();
    }

    public function verifyToken($token)
    {
        $user = User::where('confirmation_token', $token)->first();
        if ($user->status === UserStatus::UNCONFIRMED) {
            $user->status = UserStatus::ACTIVE;
            $user->save();
            \Session::flash('success', ClassMessages::EMAIL_VERIFIED);
            return redirect()->route('login');
        } elseif ($user->status === UserStatus::ACTIVE) {
            \Session::flash('info', ClassMessages::EMAIL_ALREADY_VERIFIED);
            return redirect()->route('login');
        } elseif (!$user) {
            \Session::flash('error', ClassMessages::WRONG_ACTIVATION);
            return redirect()->route('login');
        }
    }

    public function getForgetPassword()
    {
        return view($this->forget_password);
    }

    public function processForgetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {

            $token = encrypt($user->id);
            $send_mail = \Mail::to($user->email)->send(new PasswordChanger($token, $this->email_subject));

            \Session::flash('success', ClassMessages::VERIFICATION_LINK);
            return redirect()->back();

        } else {

            \Session::flash('error', ClassMessages::EMAIL_NOT_FOUND);
            return redirect()->back();
        }
    }

    public function getChangePassword($token)
    {
        $user_id = decrypt($token);
        $user = User::where('id', $user_id)->first();

        return view($this->change_password, compact('user'));

    }

    public function processChangePassword(PasswordChangeRequest $request, $user_id)
    {
        try {
            $user = User::where('id', $user_id)->first();
            $user->password = $request->password;
            $user->save();
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors(ClassMessages::PROCESSING_ERROR);
        }
        return redirect()->route('login')->with('success', ClassMessages::PASSWORD_CHANGED);

    }

}
