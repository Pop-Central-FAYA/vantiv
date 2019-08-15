<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Services\Brands\StoreBrand;
use Illuminate\Support\Facades\Auth;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Vanguard\Http\Requests\Brand\StoreRequest;
use Vanguard\Http\Resources\BrandResource;
use Vanguard\Models\Brand;
use Vanguard\Http\Requests\Brand\UpdateRequest;
use Vanguard\Services\Brands\UpdateBrand;
use Vanguard\User;



class BrandController extends Controller
{
    use CompanyIdTrait;
    public function __construct()
    {
        $this->middleware('permission:create.client')->only(['create']);
        $this->middleware('permission:update.client')->only(['update', 'destroy']);

    }

    public function create(StoreRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
       
        $new_brand = new StoreBrand($request, $request->client_id, $user->id);
        $brand = $new_brand->run();   

        $resource = new BrandResource(Brand::find($brand->id));
        return $resource->response()->setStatusCode(201);
    }
    
      /**
     * Update fields that have changed in Brand
     */
    public function update(UpdateRequest $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $this->authorize('update', $brand);

        $validated = $request->validated();
        (new UpdateBrand($brand, $validated))->run();

        $resource = new BrandResource(Brand::find($id));
        return $resource->response()->setStatusCode(200);
    }
   
}
