<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\User\UserInviteRequest;
use Vanguard\Services\User\InviteService;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use Vanguard\Services\RolesPermission\ListRoleGroup;
use Vanguard\Http\Requests\User\UpdateUserRequest;
use Vanguard\Services\User\UpdateService;
use Vanguard\Services\User\ReinviteService;
use Vanguard\User;
use Vanguard\Models\Company;
class UsersController extends Controller
{
    use CompanyIdTrait;
    const EMAIL_SUBJECT = "Invitation to join Vantage";

    public function __construct()
    {
        $this->middleware('permission:create.user')->only(['create']);
        $this->middleware('permission:update.user')->only(['update']);
        $this->middleware('permission:view.user')->only(['list']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {    
        $role_list_services = new ListRoleGroup('dsp');
        $roles = $role_list_services->getRoles();
        $urls = [
                 'list' => route('users.list'),
                 'create' => route('users.invite')
                ];
        return view('agency.user.index')
               ->with('roles', $roles)
               ->with('url', $urls);
    }


    /*******************************
     *  BELOW ARE THE API ACTIONS
     *******************************/

    /**
     * UserInviteRequest $request
     */
    
    public function create(UserInviteRequest $request)
    {
       
        $validated = $request->validated(); 
        $invite_user = new InviteService($validated, $this->companyId(), 'web', self::EMAIL_SUBJECT);
        $new_user = $invite_user->run();   
        return ['status'=>"success", 'message'=> "User(s) invited successfully, and emails sent"];
    }

     /**
     * Return a list of user that the currently logged in user has permission to view
     */
    public function list()
    {        
        $user = \Auth::user();
        $this->authorize('get', $user);
        $company_user = Company::with('users')->findOrFail($this->companyId());
        $user_list = $company_user->users;
        return new UserCollection($user_list);
    }

     /**
     * Update the user
     */
    
    public function update(UpdateUserRequest $request, $id)
    {
        $validated = $request->validated(); 
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $update_user_service = new UpdateService($validated, $this->companyId(), $id, 'web');
        $updated_user = $update_user_service->run();
        return new UserResource($updated_user);
    }

    public function resend($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $reinvite = new ReinviteService($user, self::EMAIL_SUBJECT);
        $user_reinvite = $reinvite->run();        
        return  $user_reinvite; 
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        $user->delete();
        return ['status'=>"success", 'message'=> "User deleted successfully"];
    }
   
}
