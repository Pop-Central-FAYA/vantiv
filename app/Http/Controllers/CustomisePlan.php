<?php

namespace Vanguard\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Vanguard\MediaPlan;

class CustomisePlan extends Controller
{
    public function getAll($media_plan_id)
    {
         $media_plan = MediaPlan::find($media_plan_id);
         return  $media_plan;
    }


}
