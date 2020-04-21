<?php

namespace App\Services\Merchants;

use App\User;
use App\Merchants;
use App\MerchantUsers;

class UserMerchantService
{
    protected $merchants, $users;

    public function __construct(Merchants $m, User $u)
    {
            $this->merchants = $m;
            $this->users = $u;
    }

    public static function assign(User $user)
    {
        return new self(new Merchants(),$user);
    }


    public function to(Merchants $m)
    {
        $results = false;

        $record = MerchantUsers::whereUserUuid($this->users->uuid)
            ->whereMerchantUuid($m->uuid)
            ->first();

        if(is_null($record))
        {
            $results = MerchantUsers::firstOrCreate([
                'user_uuid' => $this->users->uuid,
                'merchant_uuid' => $m->uuid,
                'active' => 1
            ]);
        }
        else
        {
            $results = $record;
        }

        return $results;
    }
}
