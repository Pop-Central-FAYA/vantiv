<?php

namespace Vanguard\Http\Controllers;

use Session;
use Faker\Factory;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Brands\StoreBrand;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Brand\StoreRequest;
use Vanguard\Http\Resources\ClientResource;
use Vanguard\Models\Brand;


class BrandController extends Controller
{
    use CompanyIdTrait;
    public function __construct()
    {
        $this->middleware('permission:create.brand')->only(['storeBrand']);
    }

    public function storeBrand(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
       
        $new_brand = new StoreBrand($request, $reques->client_id, $user->id);
        $brand = $new_brand->run();   

        $resource = new BrandResource(Brand::find($brand->id));
        return $resource->response()->setStatusCode(201);
    }
    
}
