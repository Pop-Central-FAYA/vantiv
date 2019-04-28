<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Mail\InviteUser as InviteUserMail;
use Vanguard\Services\Mail\UserInvitationMail;
use Vanguard\Services\User\GetUserList;
use Vanguard\Services\User\InviteUser;
use Vanguard\Services\RolesPermission\ListRoleGroup;
use Illuminate\Http\Request;
use Vanguard\Services\User\UpdateUser;
use Vanguard\Services\Validator\ValidateUserCompleteAccount;
use Vanguard\Services\Validator\ValidateUserInviteRequest;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;
use \Illuminate\Support\Facades\URL;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    use CompanyIdTrait;

    public function index()
    {
        $user_list_service = new GetUserList($this->getCompanyIdsList());
        $user_list = $user_list_service->getUserData();
        $roles_service = new ListRoleGroup('ssp');
        return view('users.index')->with('users', $user_list)
                                        ->with('roles', $roles_service->getRoles())
                                        ->with('companies', $this->getCompaniesDetails($this->companyId()));
    }

    public function getDatatable(DataTables $dataTables)
    {
        $user_list_service = new GetUserList($this->getCompanyIdsList());
        $user_list = $user_list_service->getUserData();
        return $dataTables->collection($user_list)
            ->addColumn('edit', function ($user_list) {
                return '<a href="#user_modal_'.$user_list['id'].'" class="weight_medium modal_user_click">Edit</a>';
            })
            ->addColumn('status', function ($user_list) {
                return '<a href="" class="weight_medium">'.$user_list['status'].'</a>';
            })
            ->rawColumns(['edit' => 'edit', 'status' => 'status'])->addIndexColumn()
            ->make(true);
    }

    public function inviteUser()
    {
        $role_list_services = new ListRoleGroup('ssp');
        $roles = $role_list_services->getRoles();
        if(!\Auth::user()->hasRole('ssp.super_admin')){
            $roles = collect($roles)->filter(function($role) {
                return $role['role'] !== 'ssp.super_admin';
            });
        }
        return view('users.invite_user')
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
        if(isset($request->companies)) {
            $companies = $request->companies;
        }else{
            $companies = \Auth::user()->companies->first()->id;
        }
        $inviter_name = \Auth::user()->full_name;
        \DB::transaction(function () use ($request, $companies, $inviter_name) {
            $user_mail_content_array = [];
            foreach ($request->email as $email) {
                $invite_user_service = new InviteUser($request->roles, $companies, $email);
                $invited_user = $invite_user_service->createUnconfirmedUser();
                $user_mail_content_array[] = [
                    'companies' => collect($invited_user->companies()->pluck('name'))->implode(', '),
                    'recipient' =>  $invited_user->email,
                    'subject' => 'New User Invitation',
                    'inviter' => $inviter_name,
                    'user_id' => $invited_user->id,
                    'link' =>  URL::temporarySignedRoute('user.complete_registration', now()->addHour(1),
                                                    ['id'=> $invited_user->id])
                ];
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
        $user = User::findOrFail($id);
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
}
