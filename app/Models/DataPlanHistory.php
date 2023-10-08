<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataPlanHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_plan_histories';

    protected $fillable = [
        'data_plan_id',
        'phone_number',
        'transaction_id',
    ];
}
