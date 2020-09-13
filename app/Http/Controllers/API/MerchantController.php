<?php

namespace App\Http\Controllers\API;

use App\Merchants;
use App\ShopifyInstalls;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Silber\Bouncer\BouncerFacade as Bouncer;

class MerchantController extends Controller
{
    use Helpers;

    protected $merchants, $request;

    public function __construct(Request $request, Merchants $merchants)
    {
        $this->request = $request;
        $this->merchants = $merchants;
    }

    public function index()
    {
        $results = ['success' => false, 'results' => 'Invalid User'];

        $user = auth()->user();

        if(!is_null($user))
        {
            // @todo - if header has a different merchant_uuid and the user is a merchant-admin
            // @todo - query for that merchant instead or deny access.

            $merchant = $user->merchant()
                ->with('shopify_installs')
                ->first();

            if(!is_null($merchant))
            {
                $payload = $merchant->toArray();
                // @todo - add other related merchant data
                $results = ['success' => true, 'merchant' => $merchant];
            }
            else
            {
                $results['reason'] = 'Could not locate merchant!';
            }
        }

        return $this->response()->array($results);
    }

    public function link_to_shopify(ShopifyInstalls $installs)
    {
        $results = [
            'success' => false,
            'reason' => 'Invalid Permissions',
            'msg' => 'You must be the account owner to link a Shopify Shop to AllCommerce.'
        ];

        if(Bouncer:: is(auth()->user())->a('merchant-owner'))
        {
            $data = $this->request->all();

            if(array_key_exists('shop', $data))
            {
                $install = $installs->whereShopifyStoreUrl($data['shop'])->first();
                $merchant = auth()->user()->merchant();

                if(!is_null($install))
                {
                    if($install->installed == 1)
                    {
                        if(is_null($install->merchant_uuid))
                        {
                            // @todo - potentially validate for null
                            $install->merchant_uuid = $merchant->uuid;

                            $install_count = $installs->whereMerchantUuid($merchant->uuid)->get();
                            if(count($install_count) == 0)
                            {
                                $install->default_store = 1;
                            }

                            if($install->save())
                            {
                                // @todo - this could prolly go somewhere better
                                Bouncer::allow(auth()->user())->to(['view', 'update'], $merchant);

                                // @todo - make sure this is one line that gets reversed if they unlink or shopify says so or gdrp
                                Bouncer::allow($merchant)->to(['view', 'update', 'unlink'], $install);

                                $results = ['success' => true];
                            }
                            else
                            {
                                $results['reason'] = 'There was an issue.';
                                $results['msg'] = 'It\'s our fault, though, maybe try again.';
                            }
                        }
                        else
                        {
                            if($install->merchant_uuid == $merchant->uuid)
                            {

                                $results = ['success' => true];
                            }
                            else
                            {
                                $results['reason'] = 'Shop Assigned to Another Merchant.';
                                $results['msg'] = 'An account can have multiple shops. But a shop can only have one merchant. And that\'s not you..';
                            }
                        }
                    }
                    else
                    {
                        $results['reason'] = 'Shop Not Installed';
                        $results['msg'] = 'Delete the app and re-install.';
                    }
                }
                else
                {
                    $results['reason'] = 'Invalid Shopify Shop';
                    $results['msg'] = 'We can\'t find this shop on file. If you are using the API instead of the Shopify Admin, stop.';
                }
            }
            else
            {
                $results['reason'] = 'Missing Shopify Shop';
                $results['msg'] = 'If you are using the API instead of the Shopify Admin, stop.';
            }
        }

        return $this->response()->array($results);
    }

    public function linked_shop_channels()
    {
        // @todo - scale this to support the merchant in the header
        // @todo - as well as permissions
        $results = ['success' => false, 'results' => 'Invalid User'];

        $user = auth()->user();
        $channels = [
            'shopify' => []
        ];
        $payload_channels = [];

        if(!is_null($user))
        {
            $merchant = $user->merchant();

            // See if this merchant has any active shop channels
            // Right now there is only one shop channel integrated - shopify
            // @todo - this is where we check for future installs as well.
            foreach($channels as $channel => $installs)
            {
                switch($channel)
                {
                    case 'shopify':
                        $installs = $merchant->shopify_installs()->get();

                        if(count($installs) > 0)
                        {
                            $payload_channels['shopify'] = [];

                            foreach ($installs as $install)
                            {
                                $payload_channels['shopify'][] = [
                                    'store_url' => $install->shopify_store_url,
                                    'uuid' => $install->uuid,
                                    'default_shop' => $install->default_store
                                ];
                            }
                        }
                        break;

                    default:
                }
            }

            $results = ['success' => true, 'channels' => $payload_channels];
        }

        return $this->response()->array($results);
    }
}
