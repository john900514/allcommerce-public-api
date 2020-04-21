<?php

namespace App\Actions\Users;

use App\User;
use Silber\Bouncer\BouncerFacade as Bouncer;

class GetAllUsers
{
    protected $users_model;

    public function __construct(User $user)
    {
        $this->users_model = $user;
    }

    public function execute()
    {
        $results = false;

        $user = auth()->user();
        $user_merchant = $user->merchant();

        if(Bouncer::is($user)->a('dev-god', 'dev'))
        {
            // Dev God receives an array with each client name as the key
            $users = $this->users_model->all();
            $results = [];
            foreach($users as $user_record)
            {
                $merchant = $user_record->merchant();

                // if the user is a god, proceed
                // if the user merchant's name is = dev user's merchant name
                if(Bouncer::is($user)->a('dev-god') || $merchant->name == $user_merchant->name)
                {
                    if(!array_key_exists($merchant->name, $results))
                    {
                        $results[$merchant->name] = [];
                    }

                    $results[$merchant->name][] = $user_record->toArray();
                }
            }
        }
        elseif (Bouncer::is($user)->a('platform-user', 'merchant-user')) {
            // Platform User && Merchant User Receives 1 record, themselves.
            $results = [$user];
        }
        elseif (Bouncer::is($user)->a('merchant-owner', 'merchant-admin','merchant-api-user', 'platform-admin')) {
            // Merchant Owner Receives a list of users from their assigned merchant
            // Merchant Api User Receives a list of users from their assigned merchant
            // Merchant Admin Receives a list of users from their first assigned merchant
            $users = User::join('merchant_users', 'merchant_users.user_uuid', '=', 'users.uuid')
                ->where('merchant_users.merchant_uuid', '=', $user_merchant->uuid)
                ->get();

            $results = $users->toArray();

        }

        return $results;
    }
}
