<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersActivities extends Model
{
    use HasFactory;

    protected $cast = [
        'role_id' => UserRole::class,
    ];
}
