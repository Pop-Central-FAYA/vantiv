<?php

namespace Vanguard\Http\Controllers\Agency;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\User\GetUserList;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Services\Mail\UserInvitationMail;
use Vanguard\Services\User\InviteUser;
use Vanguard\Services\RolesPermission\ListRoleGroup;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Services\User\UpdateUserService;
use Vanguard\Services\Validator\UpdateUserValidation;
use Vanguard\Services\Validator\ValidateUserCompleteAccount;
use Vanguard\Services\Validator\ValidateUserInviteRequest;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use \Illuminate\Support\Facades\URL;
use Yajra\DataTables\DataTables;
use Vanguard\Services\Mail\MailFormat;

class UserController extends Controller
{
    use CompanyIdTrait;

    // This fub=nction will return a view with the lis of users
    public function index()
    {
     $user_list_service = new GetUserList($this->getCompanyIdsList());
       $user_list = $user_list_service->getUserData();
       return view('agency.user-management.index')->with('users', $user_list);
    }

    public function getDatatable(DataTables $dataTables)
    {
        $user_list_service = new GetUserList($this->getCompanyIdsList());
        $user_list = $user_list_service->getUserData();
        $statuses = UserStatus::lists();
        return $dataTables->collection($user_list)
            ->addColumn('edit', function ($user_list) {
             
                    return '<a href="'.route('agency.user.edit', ['id' => $user_list['id']]).'" class="weight_medium">Edit</a>';
            
            })
            ->addColumn('status', function ($user_list) use($statuses) {
                
                    return '<a href="#user_modal_'.$user_list['id'].'" class="weight_medium modal_user_click">'.$user_list['status'].'</a>';
                
            })
            ->rawColumns(['edit' => 'edit', 'status' => 'status'])->addIndexColumn()
            ->make(true);
    }
    public function inviteUser()
    {
        $role_list_services = new ListRoleGroup('dsp');
        $roles = $role_list_services->getRoles();
        return view('agency.user-management.invite_user')
                    ->with('roles', $roles)
                    ->with('companies', $this->getCompaniesDetails($this->companyId()));
    }

    public function processInvite(Request $request)
    {
        $validate_request_service = new ValidateUserInviteRequest($request->all());
        $validate_request = $validate_request_service->validateRequest();
        if($validate_request->fails()){
            return ['status'=>"error", 'message'=> $validate_request->errors()->first()];
        }
        $companies = $this->getCompany($request->companies);
        $inviter_name = \Auth::user()->full_name;
        \DB::transaction(function () use ($request, $companies, $inviter_name) {
            $user_mail_content_array = [];
            foreach ($request->email as $email) {
                $invite_user_service = new InviteUser($request->roles, $companies, $email, "dsp");
                $invited_user = $invite_user_service->createUnconfirmedUser();

                $email_format = new MailFormat($invited_user, $inviter_name);
                $user_mail_content_array[] = $email_format->emailFormat();
            }
            $email_invitation_service = new UserInvitationMail($user_mail_content_array);
            $email_invitation_service->sendInvitationMail();
        });
        return ['status'=>"success", 'message'=> "User(s) invited successfully, and emails sent"];
    }

    public function getCompleteAccount(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            \Session::flash('error', 'Invalid/Expired link, contact admin');
            return redirect()->route('login');
        }
        $user = AgencyUser::findOrFail($id);
        return view('auth.complete_registration')->with('user', $user);
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
// retun the view to add role to a member 
    public function editUser($id)
    {
        $user = User::find($id)->load('companies');
        $roles_service = new ListRoleGroup('dsp');
        return view('agency.user-management.edit')->with('roles', $roles_service->getRoles())
                                        ->with('companies', $this->getCompaniesDetails($this->companyId()))
                                        ->with('user', $user);
    }

    public function updateUser($id, Request $request)
    {
        $validate_request_service = new UpdateUserValidation($request->all());
        $validate_request = $validate_request_service->validateRequest();
        if($validate_request->fails()){
            return ['status'=>"error", 'message'=> $validate_request->errors()->first()];
        }

        $update_user_service = new UpdateUserService($request->roles, $this->getCompany($request->companies), $request->user_id, 'dsp');
        $update_user_service->updateUser();
        return ['status'=>"success", 'message'=> "User updated successfully"];
    }

    private function getCompany($request)
    {
        if(isset($request->companies)) {
            $companies = $request->companies;
        }else{
            $companies = \Auth::user()->companies->first()->id;
        }
        return $companies;
    }

    public function resendInvitation(Request $request)
    {
        $user = User::find($request->user_id);

        $email_format = new MailFormat($user, \Auth::user()->full_name);
        $user_mail_content_array[] = $email_format->emailFormat();

        $email_invitation_service = new UserInvitationMail($user_mail_content_array);
        $email_invitation_service->sendInvitationMail();
        return ['status'=>"success", 'message'=> "Invitation has been sent to the user"];
    }
 
    public function updateStatus(Request $request)
    {
        try{
            $user = User::findOrFail($request->user_id);
            $user->status = $request->status;
            $user->save();
        }catch (\Exception $exception){
            \Log::error($exception);
            return ['status'=>"error", 'message'=> 'An error occurred while performing your request, please contact admin'];
        }
        return ['status'=>"success", 'message'=> "Status updated successfully"];
    }
    
}
?>