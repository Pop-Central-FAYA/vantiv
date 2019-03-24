<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class MediaPlan extends Model
{
    protected $fillable = ['plan_id', 'campaign_name', 'product_name', 'gender', 'client_id', 'brand_id', 'start_date', 'end_date', 'total_budget', 'actual_spend', 'total_target_reach', 'actual_reach', 'lsms', 'regions', 'status'];
}