<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\FileUploadTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, FileUploadTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string> test
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

    protected $appends = ['is_admin'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function deviceToken()
    {
        return $this->hasOne(DeviceToken::class);
    }

    public function setProfilePicAttribute($value)
    {
        $this->saveFile($value, 'profile_pic', "user/" . date('Y/m'));
    }

     public function getProfilePicAttribute()
    {
        if (empty($this->attributes['profile_pic'])) {
            return config('app.url')."/images/user.webp";
        } else {
            return $this->getFileUrl($this->attributes['profile_pic']);
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function getIsAdminAttribute()
    {
        $ids = $this->roles->pluck('role_id');
        $roles = Role::whereIn('id', $ids)->pluck('name')->contains('Admin');
        return $roles;
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    public function routeNotificationForFcm($notification)
    {
        return $this->deviceToken->pluck('device_token')->toArray();
    }
    
    public function roleName()
    {
        return $this->belongsTo('App\Models\Role','role_id','id');
    }
}
