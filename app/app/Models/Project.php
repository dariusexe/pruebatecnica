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

    public function isParticipant($user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
    public function isParticipantWithRole($user, $role)
    {
        return $this->users()->where('user_id', $user->id)->where('role_id', $role)->exists();
    }

}
