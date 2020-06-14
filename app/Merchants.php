<?php

namespace App;

use Bouncer;
use App\User;
use App\Traits\UuidModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Merchants extends Model
{
    use HasRolesAndAbilities, LogsActivity, Notifiable, SoftDeletes, UuidModel;

    protected $hidden = ['id', 'deleted_at'];

    public function permissions(User $user)
    {
        $results = [];

        $abilities = $this->getAbilities();
        $forbidden = $this->getForbiddenAbilities();

        foreach ($abilities as $ability)
        {
            $results[$ability->name] = true;
        }

        foreach ($forbidden as $ability)
        {
            $results[$ability->name] = false;
        }

        foreach ($results as $ability => $toggle)
        {
            if($toggle && $user->cannot($ability))
            {
                Bouncer::allow($user)->to($ability);
                Bouncer::unforbid($user)->to($ability);
            }
            else if((!$toggle))
            {
                Bouncer::forbid($user)->to($ability);
            }
        }

        return $results;
    }

    public function inventory()
    {
        return $this->hasMany('App\MerchantInventory', 'merchant_uuid', 'uuid');
    }

    public function shopify_installs()
    {
        return $this->hasMany('App\ShopifyInstalls', 'merchant_uuid', 'uuid');
    }

    public function merchant_users()
    {
        return $this->hasMany('App\MerchantUsers', 'merchant_uuid', 'uuid')
            ->with('user');
    }

    public function merchant_owner()
    {
        $results = false;

        $users = $this->merchant_users()
            ->get();

        if(count($users) > 0)
        {
            foreach ($users as $merchant_user)
            {

                if(Bouncer::is($merchant_user->user)->a('merchant-owner'))
                {
                    $results = $merchant_user->user;
                    break;
                }
            }
        }

        return $results;
    }
}
