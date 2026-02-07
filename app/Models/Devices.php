<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DeviceSwitchName;

class Devices extends Model
{
    use HasFactory;

    protected $table = 'assign_device';

    protected $fillable = [
        'user_id',
        'device_id',
        'status',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function switchNames()
    {
        return $this->hasOne(DeviceSwitchName::class,'assign_device_id', 'id')->withDefault();
    }
}
