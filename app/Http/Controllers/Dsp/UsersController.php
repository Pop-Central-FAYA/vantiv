<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\User\UserInviteRequest;
use Vanguard\Services\User\InviteService;


class UsersController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:create.user')->only(['create']);
        $this->middleware('permission:update.user')->only(['update']);
        $this->middleware('permission:view.user')->only(['list', 'get']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {   
       
    }


    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * Return a list of client that the currently logged in user has permission to view
     * Filter parameters are allowed
     */
    
    public function create(UserInviteRequest $request)
    {
        $validated = $request->validated();
        $invite_user = new InviteService($validated, $this->companyId(), 'web');
        $new_user = $invite_user->run();   
        return response()->setStatusCode(201);
    }

   
}
