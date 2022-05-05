<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Enum\UserRole;

class Permissions extends Pivot
{
    use HasFactory;

    protected $cast = [
        'role_id' => UserRole::class,
    ];
    


}
