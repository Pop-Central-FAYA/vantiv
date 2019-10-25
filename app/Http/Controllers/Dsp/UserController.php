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
use Vanguard\Mail\SendUserInvitationMail;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\Services\Validator\ValidateUserCompleteAccount;
use Session;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Libraries\ActivityLog\LogActivity;
class UserController extends Controller
{
    use CompanyIdTrait;

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
        $user = \Auth::user()->getCompanyName();
        $role_list_services = new ListRoleGroup('dsp');
        $roles = $role_list_services->getRoles();
        $routes = [
                 'list' => route('users.list'),
                 'create' => route('users.invite')
                ];
        return view('agency.user.index')
               ->with('roles', $roles)
               ->with('routes', $routes);
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
        $user = \Auth::user();
        $invite_user = new InviteService($validated, $user);
        $new_user = $invite_user->run();   
        $logactivity = new LogActivity($new_user, "Created");
        $log = $logactivity->log();
        return new UserResource($new_user);
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
        $logactivity = new LogActivity($updated_user, "Updated");
        $log = $logactivity->log();
        return new UserResource($updated_user);
    }

    public function resend(Request $request, $id)
    {  
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
        $send_mail = \Mail::to($user->email)->send(new SendUserInvitationMail($user, $user->full_name));
        $logactivity = new LogActivity($user, "Resent Invitaion");
        $log = $logactivity->log();
        return response()->json(array(
            'code' =>  204,
           ),204); 
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);
        $user->delete();
        $logactivity = new LogActivity($user, "Delete");
        $log = $logactivity->log();
          return response()->json(array(
                 'code' =>  204,
                ),204); 
    }

    public function getCompleteAccount(Request $request, $id)
    {

        if (!$request->hasValidSignature()) {
            \Session::flash('error', 'Invalid/Expired link, contact admin');
            return redirect()->route('login');
        }
        $user = User::findOrFail($id);
        if($user->status !== UserStatus::UNCONFIRMED ){
            Session::flash('error', 'You have already completed your registration, please login with your credentials');
            return redirect()->route('login');
        }

        return view('auth.dsp.complete_registration')->with('user', $user);
    }

    public function processCompleteAccount(Request $request, $id)
    {
        $validate_request_service = new ValidateUserCompleteAccount($request->all());
        $validate_request = $validate_request_service->validateRequest();
        if($validate_request->fails()){
            return ['status'=>"error", 'message'=> $validate_request->errors()->first()];
        }
        \DB::transaction(function () use($request, $id) {
            $update_user_service = new UpdateUser($id, $request->firstname, $request->lastname, '',
                '', '', $request->password, 'complete_registration', UserStatus::ACTIVE);
            $update_user_service->updateUser();
            $update_user_service->updatePassword();
        });

        return ['status'=>"success", 'message'=> "Thank you for completing your registration, you can now login with your credentials"];
    }

   
}
