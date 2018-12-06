<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Model;

class WalletHistory extends Model
{
    protected $table = 'walletHistories';
    protected $connection = 'api_db';
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $dates = [
        'time_created', 'time_modified'
    ];

    protected $fillable = [
        'id', 'user_id', 'amount', 'prev_balance', 'status', 'current_balance'
    ];
}
