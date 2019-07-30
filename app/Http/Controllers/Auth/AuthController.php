<?php

namespace Vanguard\Http\Controllers\Auth;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Vanguard\Events\User\LoggedIn;
use Vanguard\Events\User\LoggedOut;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Auth\LoginRequest;
use Vanguard\Http\Requests\User\PasswordChangeRequest;
use Vanguard\Libraries\Enum\ClassMessages;
use Vanguard\Libraries\Enum\UserStatus;
use Vanguard\Mail\PasswordChanger;
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

        //Redirect URL that can be passed as hidden field.
        $to = $request->has('to') ? "?to=" . $request->get('to') : '';

        $credentials = $this->getCredentials($request);

        if (!Auth::validate($credentials)) {
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

        if (!$this->isRightUser($user)) {
            return redirect()->to(route('login'))
                ->with('error', ClassMessages::INVALID_EMAIL_PASSWORD);
        }

        Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember);

        $this->setUserSession($user);

        return $this->handleUserWasAuthenticated($request, $user);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  Request $request
     * @param  bool $throttles
     * @param $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request, $user)
    {
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
        return [
            'email' => $request->email,
            'password' => $request->password
        ];
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
