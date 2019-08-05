<?php

namespace Vanguard\Http\Controllers;

use Vanguard\Libraries\AmazonS3;
use Vanguard\Libraries\Enum\ClassMessages;
use Auth;
use Illuminate\Http\Request;
use Vanguard\Services\Company\UpdateCompany;
use Vanguard\User;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;

class CompanyController extends Controller
{
    use CompanyIdTrait;
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(Request $request)
    {
       /* if($request->image_url){
            $this->uploadImage($request);
        }
        $update_company_service = new UpdateCompany($this->companyId(), $request->address, "");
        $update_company_service->run();

        Session::flash('success', ClassMessages::COMPANY_UPDATE_SUCCESS);
        return redirect()->back(); */
        return response()->json([
            'status' => 'success',
            'created' => true,
            ]);
    }


    public function uploadImage($request)
    {
        $this->validate($request, [
            'image_url' => 'required|image|mimes:jpg,jpeg,png',
        ]);
        $avatar = $request->image_url;
        $filename = realpath($avatar);
        $key = 'profile-images/'.uniqid().'-'.$avatar->getClientOriginalName();
        $image_url = AmazonS3::uploadToS3FromPath($filename, $key);

        $update_company_service = new UpdateCompany($this->companyId(), "", $image_url);
        return $update_company_service->updateAvatar();
    }



}
