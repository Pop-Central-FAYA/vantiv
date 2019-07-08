<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Auth\AuthController;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Http\Requests\User\PasswordChangeRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Enum\CompanyTypeName;
use Vanguard\Libraries\Enum\UserStatus;
use Vanguard\Mail\PasswordChanger;
use Vanguard\Models\Agency;
use Vanguard\Models\Broadcaster;
use Vanguard\Repositories\User\UserRepository;
use Vanguard\Services\Auth\TwoFactor\Contracts\Authenticatable;
use Auth;
use Carbon\Carbon;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\User\AuthenticatableUser;
use Vanguard\User;

class DspAuthController extends AuthController
{

    public function getLogin()
    {
        return view('auth.dsp_login');
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


        if (! Auth::validate($credentials)) {

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            if ($throttles) {
                $this->incrementLoginAttempts($request);
            }

            return redirect()->to(route('dsplogin') . $to)
                ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);


        if ($user->isUnconfirmed()) {
            return redirect()->to(route('dsplogin') . $to)
                ->with('error', ClassMessages::EMAIL_CONFIRMATION);
        }

        if ($user->isBanned()) {
            return redirect()->to(route('dsplogin')  . $to)
                ->with('error', ClassMessages::BANNED_ACCOUNT);
        }

       Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember);

        if(Auth::guard('web')->user()->status === 'Unconfirmed' || Auth::guard('web')->user()->status === ''){
           Auth::logout();
        }

       if(Auth::guard('web')->user()->company_type == CompanyTypeName::AGENCY){
            session()->forget('broadcaster_id');
            session(['agency_id' => Auth::guard('web')->user()->companies->first()->id]);
        }else{
           return redirect()->to(route('login')  . $to)
            ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        return $this->handleDspUserWasAuthenticated($request, $throttles, $user);

    }

        /**
     * Send the response after the user was authenticated.
     *
     * @param  Request $request
     * @param  bool $throttles
     * @param $user
     * @return \Illuminate\Http\Response
     */

    protected function handleDspUserWasAuthenticated(Request $request, $throttles, $user)
    {


        if ($throttles) {
            $this->clearLoginAttempts($request);
        }

        $update_user = User::find($user->id);
        $update_user->last_login = Carbon::now();
        $update_user->save();




            event(new LoggedIn($user));
            return redirect()->intended(route('dashboard'));

    }

    protected function logoutAndRedirectToTokenPage(Request $request, Authenticatable $user)
    {
        Auth::guard('web')->logout();

        $request->session()->put('auth.2fa.id', $user->id);

        return redirect()->route('auth.token');
    }


    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */

    public function getLogout()
    {
        event(new LoggedOut(Auth::guard('web')->user()));

        Auth::guard('web')->logout();

        \Session::flush();

        return redirect(route('login'));
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

    public function getForgetPassword()
    {
        return view('auth.dsp_password.forget_password');
    }

    public function processForgetPassword(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required',
        ]);

        $user = User::where('email', $request->email)->first();
        if($user){

            $token = encrypt($user->id);

            $send_mail = \Mail::to($user->email)->send(new PasswordChanger($token));

            \Session::flash('success', ClassMessages::VERIFICATION_LINK);
            return redirect()->back();

        }else{

            \Session::flash('error', ClassMessages::EMAIL_NOT_FOUND);
            return redirect()->back();
        }
    }

    public function getChangePassword($token)
    {
        $user_id = decrypt($token);
        $user = User::where('id', $user_id)->first();

        return view('auth.dsp_password.change_password', compact('user'));

    }

    public function processChangePassword(PasswordChangeRequest $request, $user_id)
    {
        try{
            $user = User::where('id', $user_id)->first();
            $user->password = $request->password;;
            $user->save();
        }catch (\Exception $exception){
            return redirect()->back()->withErrors(ClassMessages::PROCESSING_ERROR);
        }
        return redirect()->route('dsplogin')->with('success', ClassMessages::PASSWORD_CHANGED);

    }


}
