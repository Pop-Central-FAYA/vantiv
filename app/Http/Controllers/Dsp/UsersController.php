<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\User\UserInviteRequest;
use Vanguard\Services\User\InviteService;
use Vanguard\Http\Requests\User\ListRequest;
use Vanguard\Services\User\UserListService;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use Vanguard\Services\RolesPermission\ListRoleGroup;


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
        $role_list_services = new ListRoleGroup('dsp');
        $roles = $role_list_services->getRoles();
        return view('agency.user.index')
               ->with('roles', $roles);
    }


    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * Return a list of client that the currently logged in user has permission to view
     * Filter parameters are allowed
     * UserInviteRequest $request
     */
    
    public function create(UserInviteRequest $request)
    {
        $validated = $request->validated(); 
        $invite_user = new InviteService($request, $this->companyId(), 'web');
        $new_user = $invite_user->run();   
        return ['status'=>"success", 'message'=> "User(s) invited successfully, and emails sent"];
      
    }

     /**
     * Return a list of ad vendors that the currently logged in user has permission to view
     * Filter parameters are allowed
     * No need for a service now, until the query gets more complicated
     */
    public function list(ListRequest $request)
    {        
        $validated = $request->validated();
        $validated['company_id'] = $this->companyId();   
        $user_list_service = new UserListService($this->getCompanyIdsList());
        $user_list = $user_list_service->run();
        return new UserCollection($user_list);
    }

   
}
