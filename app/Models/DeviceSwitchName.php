<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceSwitchName extends Model
{
    use HasFactory;

    protected $table = 'device_switch_name';

    protected $fillable = [
        'assign_device_id',
        'switch0','switch1','switch2','switch3',
        'switch4','switch5','switch6','switch7',
    ];
}
