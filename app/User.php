<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Zizaco\Entrust\Traits\EntrustUserTrait;
use App\Fusion\Interfaces\UserInterface;

class User extends Authenticatable implements UserInterface
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
        if ($this->attributes['roles'] == 'administrator'){
            return true;
        } else {
            return false;
        }
    }

    public function isWarehouse()
    {
        if ($this->attributes['roles'] == 'warehouse'){
            return true;
        } else {
            return false;
        }
    }

    public function getRoleId()
    {
        return $this->attributes['role_id'];
    }
}
