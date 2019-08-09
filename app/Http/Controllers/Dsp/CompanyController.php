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

class CompanyController extends Controller
{
    use CompanyIdTrait;
    public function index(Request $request)
    {   
        $company =  Company::findOrFail($this->companyId());
        return view('agency.company.index')->with('company', $company);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDetails(UpdateRequest $request, $id)
    {
        $company =  Company::findOrFail($id);
        $validated = $request->validated();
        $update_company_service = new UpdateCompany($company,  $request);
        $update_company_service->run();

        return new CompanyResource(Company::find($id));
    }
}
