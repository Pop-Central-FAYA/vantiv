<?php

namespace Vanguard\Http\Controllers\Dsp;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Profile\UpdateRequest;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Services\Profile\UpdateService;
use Vanguard\User;
use Vanguard\Services\Profile\UpdatePassword;
use Vanguard\Http\Requests\Profile\UpdatePasswordRequest;
use Vanguard\Http\Requests\Profile\ResetPasswordRequest;
use Vanguard\Libraries\ActivityLog\LogActivity;

class ProfileController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:view.profile')->only(['get']);
        $this->middleware('permission:update.profile')->only(['update']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {
        $user = \Auth::user();
        $routes = [
            'presigned_url' => route('presigned.url', false),
            'change_password' => route('password.update', false),
        ];
        return view('agency.profile.index')
            ->with('routes', $routes)
            ->with('user', new UserResource($user));
    }

    public function resetPassword(Request $request,$token)
    {
        $routes = [
            'change_password' => route('process.password.reset', false),
            'login' => route('login', false),
        ];
        return view('agency.profile.change_password')
            ->with('routes', $routes)
            ->with('token', $token);
    }
    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * get profile details
     */

    public function get(Request $request)
    {
        $user = \Auth::user();
        $this->authorize('getProfile', $user);
        return new UserResource($user);
    }

    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        $this->authorize('updateProfile', $user);
        (new UpdateService($user, $validated))->run();
        $logactivity = new LogActivity($user, "profile updated");
        $log = $logactivity->log();
        $resource = new UserResource(User::find($id));
        return $resource->response()->setStatusCode(200);

    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        if($user->can('update.profile')){
            (new UpdatePassword($user, $validated))->run();
            $resource = new UserResource($user);
            $logactivity = new LogActivity($user, "updated password");
            $log = $logactivity->log();
            return $resource->response()->setStatusCode(200);
        }

    }

    public function processResetPassword(ResetPasswordRequest $request)
    {
        $validated = $request->validated();
        $user= User::findOrFail(decrypt($validated['token']));
        (new UpdatePassword($user, $validated))->run();
         return response()->json(array(
            'code' =>  200,
           ),200); 
    }

}
