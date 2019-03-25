<?php

namespace Vanguard\Http\Controllers\MediaPlan;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Region;
use Vanguard\Models\TargetAudience;
use Vanguard\Models\LivingStandardMeasure;
use Vanguard\Models\WalkIns;


class MediaPlanController extends Controller
{
    public function index($value='')
    {
    	// print_r(WalkIns::get());
    }

    public function criteriaForm(Request $request)
    {
    	$regions = Region::get();
    	$genders = TargetAudience::get();
    	$lsms = LivingStandardMeasure::get();
    	$clients = WalkIns::get();

    	// return view with the above data
    }
}
