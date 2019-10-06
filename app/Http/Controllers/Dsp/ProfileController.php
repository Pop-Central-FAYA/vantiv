<?php

namespace Vanguard\Http\Controllers\Dsp;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Profile\UpdateRequest;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Services\Profile\UpdateService;
use Vanguard\User;
use Vanguard\Http\Requests\Profile\PasswordRequest;
use Vanguard\Services\Profile\UpdatePassword;

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
            'presigned_url' => route('presigned.url'),
            'change_password' => route('password.update'),
        ];
        return view('agency.profile.index')
            ->with('routes', $routes)
            ->with('user', new UserResource($user));
    }

    public function changePassword(Request $request,$token)
    {
        $user_id = decrypt($token);
        $user= User::findOrFail($user_id);
        $routes = [
            'change_password' => route('password.update'),
            'login' => route('login'),
        ];
        return view('agency.profile.change_password')
            ->with('routes', $routes)
            ->with('permissions', $user->getAllPermissions()->pluck('name'))
            ->with('user', new UserResource($user));
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

        $resource = new UserResource(User::find($id));
        return $resource->response()->setStatusCode(200);

    }

    public function updatePassword(PasswordRequest $request)
    {
        $validated = $request->validated();
        $user = User::findOrFail($validated['id']);
        if($user->can('update.profile')){
            (new UpdatePassword($user, $validated))->run();
            $resource = new UserResource(User::find($validated['id']));
            return $resource->response()->setStatusCode(200);
        }

    }

}
