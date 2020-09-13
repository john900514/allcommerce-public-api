<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Foundation\Auth\User as Authenticatable;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class User extends Authenticatable implements JWTSubject
{
    use HasRolesAndAbilities, LogsActivity, Notifiable, SoftDeletes, Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','deleted_at','email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'uuid',
        'client_id' => 'uuid',
        'email_verified_at' => 'datetime',
    ];

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

    /**
     * @return Merchants|null
     */
    public function merchant()
    {
        $through_model = $this->merchant_user_record()->first();

        if(!is_null($through_model))
        {
            return $through_model->merchant()->first();
        }

        return null;
    }

    public function merchant_user_record()
    {
        return $this->hasMany('App\MerchantUsers', 'user_uuid', 'uuid');
    }

    public function username()
    {
        return 'username'; //or return the field which you want to use.
    }

    public function client()
    {
        return $this->hasOne('App\Clients', 'id', 'client_id');
    }

    public function isHostUser()
    {
        return $this->client_id == Clients::getHostClient();
    }
}
