<?php

namespace App\Http\Controllers\API;

use App\Merchants;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Silber\Bouncer\BouncerFacade as Bouncer;

class AuthController extends Controller
{
    use Helpers;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return ['token' => $token];
    }

    /**
     * Get the authenticated User.
     *
     */
    public function me()
    {
        $user = auth()->user();
        $users_default_merchant = $user->merchant();

        $merchants = [];
        if(Bouncer::is($users_default_merchant)->an('allcommerce'))
        {
            // AllCommerce Users Can Have Access to All Merchants
            $temp = Merchants::all();
            foreach($temp as $merchant)
            {
                $merchants[$merchant->uuid] = $merchant->name;
            }
        }
        else
        {
            if(Bouncer::is($user)->a('merchant-admin'))
            {
                //Merchant Admins MAY have more than one merchant assigned
                $merchants[$users_default_merchant->uuid] = $users_default_merchant->name;
                $temp = Merchants::all();
                foreach($temp as $merchant)
                {
                    // Skip the user's default merchant
                    if($merchant->uuid != $users_default_merchant->uuid)
                    {
                        if($user->can('view', $merchant))
                        {
                            $merchants[$merchant->uuid] = $merchant->name;
                        }
                    }

                }
            }
            else
            {
                //All other Merchant Users only get access to their store
                $merchants[$users_default_merchant->uuid] = $users_default_merchant->name;
            }
        }

        $results = [
            'user' => $user,
            'token' => auth()->refresh(),
            'roles' => auth()->user()->getRoles(),
            'merchants' => $merchants,
        ];

        if(Bouncer::is($users_default_merchant)->an('allcommerce'))
        {
            $results['is_allcommerce'] = true;
            $results['capeandbay_uuid'] = $users_default_merchant->uuid;
        }
        return response()->json($results);
    }
}
