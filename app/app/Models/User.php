<?php

namespace App\Models;

use App\Enum\UserRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Permissions;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'users-projects')->withPivot('role_id');
    }
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'users-activities')->withPivot('role_id');
    }
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'users-incidents');
    }
    public function activityFromProject($project)
    {
        return $this->activities()->where('project_id', $project->id)->get();
    }
    public function activitiesWhereUserIsManager()
    {
        return $this->activities()->wherePivot('role_id', UserRole::MANAGER);
    }
    public function incidentsWhereUserIsManagerInActivity($user)
    {
        return $this->activitiesWhereUserIsManager()->whereHas('incidents')->get()->pluck('incidents')->collapse();
    }
}
