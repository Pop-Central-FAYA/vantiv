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

class ProfileController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:view.profile')->only(['get']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {
        $user = \Auth::user();
        $routes = [
            'presigned_url' => route('presigned.url'),
        ];
        return view('agency.profile.index')
            ->with('routes', $routes)
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
        $this->authorize('get', $user);
        return new UserResource($user);
    }

    public function update(UpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        $this->authorize('update', $user);
        (new UpdateService($user, $validated))->run();

        $resource = new UserResource(User::find($id));
        return $resource->response()->setStatusCode(200);

    }

    public function updatepassword(PasswordRequest $request, $id)
    {
        $validated = $request->validated();
        $user = \Auth::user();
        $this->authorize('update', $user);
        (new UpdateService($user, $validated))->run();

        $resource = new UserResource(User::find($id));
        return $resource->response()->setStatusCode(200);

    }

}
