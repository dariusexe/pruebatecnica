<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permissions;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'permissions')->using(Permissions::class);
    }
}
