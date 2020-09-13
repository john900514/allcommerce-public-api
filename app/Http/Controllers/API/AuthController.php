<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Merchants;
use App\ShopifyInstalls;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
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
        $credentials['username'] = $credentials['email'];
        unset($credentials['email']);

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
        $client = $user->client()->first();
        $merchants = $client->merchants()->get();
        $shops = $client->shops()->get();

        /*
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
        */

        $results = [
            'user' => $user,
            'token' => auth()->refresh(),
            'roles' => auth()->user()->getRoles(),
            'client' => $client->toArray(),
            'merchants' => $merchants->toArray(),
            'shops' => $shops->toArray(),
        ];

        if($user->isHostUser())
        {
            $results['is_allcommerce'] = true;
            $results['capeandbay_uuid'] = $client->getHostClient();
        }
        return response()->json($results);
    }

    public function shopify_sso(ShopifyInstalls $installs, User $users)
    {
        $results = ['success' => false, 'reason' => 'No Merchant Assigned!'];

        $data = $this->request->all();

        $validated = Validator::make($data, [
            'hmac' => 'bail|required',
            'shop' => 'bail|required',
            'timestamp' => 'bail|required',
            'session' => 'bail|required',
            'locale' => 'bail|required',
        ]);

        if ($validated->fails())
        {
            foreach($validated->errors()->toArray() as $col => $msg)
            {
                $results['reason'] = $msg[0];
                break;
            }
        }
        else
        {
            // @todo - validate the HMAC
            if(true)
            {
                $install = $installs->whereShopifyStoreUrl($data['shop'])
                    ->with('signed_in_user')
                    ->first();

                if(!is_null($install))
                {
                    if(!is_null($install->signed_in_user))
                    {
                        auth()->login($install->signed_in_user);
                        return $this->me();
                    }
                    else
                    {
                        $results['reason'] = 'No User Assigned!';
                    }

                    if(!is_null($install->merchant_id))
                    {
                        $merchant = $install->merchant()->first();

                        if(!is_null($merchant))
                        {
                            $user = $merchant->merchant_owner();
                            auth()->login($user);
                            return $this->me();
                        }
                        else
                        {
                            $results['reason'] = 'Could Not Locate Assigned Merchant!';
                        }
                    }
                }
                else
                {
                    $results['reason'] = 'Invalid Shop!';
                }
            }
            else
            {
                $results['reason'] = 'Could not validate request';
            }
        }

        return $results;
    }

}
