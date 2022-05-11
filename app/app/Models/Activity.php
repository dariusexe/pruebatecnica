<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'project_id',
    ];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function isParticipant($user)
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
    public function isParticipantWithRole($user, $role)
    {
        return $this->users()->where('user_id', $user->id)->where('role_id', $role)->exists();
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'users-activities')->withPivot('role_id');
    }
}
