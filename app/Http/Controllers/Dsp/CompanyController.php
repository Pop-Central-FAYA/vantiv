<?php
namespace Vanguard\Http\Controllers\Dsp;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\Enum\ClassMessages;
use Auth;
use Vanguard\Services\Company\UpdateCompany;
use Vanguard\User;
use Vanguard\Models\Company;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Company\UpdateRequest;
use Vanguard\Http\Resources\CompanyResource;
use Vanguard\Libraries\ActivityLog\LogActivity;

class CompanyController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
       $this->middleware('permission:update.company')->only(['update']);
    }
    
    public function index(Request $request)
    {   
        $company =  Company::findOrFail($this->companyId());
        $urls = [
            'presigned_url' => route('presigned.url'),
            'company_update' => route('company.update', ['id'=>$this->companyId()]),
        ];
        return view('agency.company.index')->with('company', $company)->with('url',$urls);
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {

        $company =  Company::findOrFail($id);
        $this->authorize('update', $company);
        $validated = $request->validated();
        $update_company_service = new UpdateCompany($company,  $request);
        $update_company_service->run();
        $logactivity = new LogActivity($company, "updated company");
        $log = $logactivity->log();
        return new CompanyResource( Company::findOrFail($id));
    }
}
