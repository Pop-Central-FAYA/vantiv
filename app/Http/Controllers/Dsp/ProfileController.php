<?php

namespace Vanguard\Http\Controllers\Dsp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Controllers\Traits\CompanyIdTrait;
use Illuminate\Http\Request;

use Vanguard\User;
use Vanguard\Http\Resources\UserResource;
use Vanguard\Http\Requests\Profile\UpdateRequest;
class ProfileController extends Controller
{
    use CompanyIdTrait;

    public function __construct()
    {
        $this->middleware('permission:view.profile')->only(['get']);
    }

    /*******************************
     * BELOW ARE THE PAGES.
     *******************************/

    public function index(Request $request)
    {    
        $user = \Auth::user();
        $routes = [
            'presigned_url' => route('presigned.url'),
        ];
        return view('agency.profile.index')
               ->with('routes', $routes)
               ->with('user', new UserResource($user));
    }



    /*******************************
     *  BELOW ARE THE API ACTIONS
    *******************************/

    /**
     * get profile details
     */

    public function get(Request $request)
    {  
        $user = \Auth::user();
        $this->authorize('get', $user);
        return new UserResource($user);
    }

    public function update(UpdateRequest $request, $id)
    {  
        $validated = $request->validated(); 
        $user = \Auth::user();
        $this->authorize('update', $user);
        return new UserResource($user);
    }


   
}
