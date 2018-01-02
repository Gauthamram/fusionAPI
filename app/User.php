<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Fusion\Contracts\UserInterface;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Fusion\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable implements UserInterface, CanResetPassword
{
    use Notifiable;
    /**
     * [$table set name of the table]
     * @var string
     */
    protected $table = "cgl_api_users";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','roles','role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * This mutator automatically hashes the password.
     *
     * @var string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = \Hash::make($value);
    }

    public function isAdmin()
    {
        if (strtolower($this->attributes['roles']) == 'administrator') {
            return true;
        } else {
            return false;
        }
    }

    public function isWarehouse()
    {
        if (strtolower($this->attributes['roles']) == 'warehouse') {
            return true;
        } else {
            return false;
        }
    }

    public function getRoleId()
    {
        return $this->attributes['role_id'];
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

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
