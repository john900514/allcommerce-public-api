<?php

namespace App\Http\Controllers\API;

use App\MerchantInventory;
use App\ShopifyInstalls;
use Dingo\Api\Http\Request;
use Ixudra\Curl\Facades\Curl;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Actions\Merchants\Inventory\GetAllInventory;

class InventoryController extends Controller
{
    use Helpers;

    protected $request, $shopify_installs;


    public function __construct(Request $request, ShopifyInstalls $installs)
    {
        $this->request = $request;
        $this->shopify_installs = $installs;
    }

    /**
     * GET /inventory
     */
    public function index(GetAllInventory $action)
    {
        $results = 'Unauthorized';
        $status = 401;

        $user = auth()->user();
        $merchant = $user->merchant();

        if(!Bouncer::is($merchant)->an('allcommerce'))
        {
            $header_uuid = $this->request->header('merchant-uuid');
            $access_merchant_uuid = (!is_null($header_uuid)) ? $header_uuid : $merchant->uuid;

            $results = $action->execute($access_merchant_uuid);

            if(is_array($results))
            {
                $status = 200;
            }
            else
            {
                $results = 'Not Found';
                $status = 404;
            }
        }
        else
        {
            $access_merchant_uuid = $this->request->header('merchant-uuid');

            if(!is_null($access_merchant_uuid))
            {
                $results = $action->execute($access_merchant_uuid);

                if(is_array($results))
                {
                    $status = 200;
                }
                else
                {
                    $results = 'Invalid Merchant';
                    $status = 405;
                }
            }
        }

        return response($results,$status);
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function show($uuid)
    {

    }

    public function edit($uuid)
    {

    }

    public function update($uuid)
    {

    }

    public function destroy($uuid)
    {

    }

    public function get_shopify_inventory()
    {
        $results = ['success' => false];

        $data = $this->request->all();

        if(array_key_exists('shop', $data))
        {
            $user = auth()->user();
            $merchant = $user->merchant();
            $install = $this->shopify_installs->whereMerchantUuid($merchant->uuid)
                ->whereShopifyStoreUrl($data['shop'])
                ->first();

            if(!is_null($install))
            {
                $headers = [
                    'X-Shopify-Access-Token: '.$install->access_token
                ];
                // Call out to shopify for product listing or fail
                $response  = Curl::to('https://'.$data['shop'].'/admin/api/2020-04/product_listings.json')
                    ->withHeaders($headers)
                    ->asJson(true)
                    ->get();

                if(is_array($response) && array_key_exists('product_listings', $response))
                {
                    //Return the product listing (:
                    $results = ['success' => true, 'listings' => $response['product_listings']];
                }
                else
                {
                    // @todo - make this shit readable
                    $results['reason'] = 'Shopify Error';
                    $results['error'] = $response;
                }
            }
            else
            {
                $results['reason'] = 'Could not Locate Shop';
            }
        }
        else
        {
            $results['reason'] = 'Missing Shop';
        }

        return $results;
    }

    public function compare_with_shopify(MerchantInventory $inventory, ShopifyInstalls $installs)
    {
        $results = $this->get_shopify_inventory();

        if($results['success'])
        {
            $user = auth()->user();
            $merchant = $user->merchant();
            $data = $this->request->all();

            // Get All Native Inventory (If empty - send back all shopify items)
            $install = $installs->whereShopifyStoreUrl($data['shop'])
                ->with('merchant_inventory')
                ->first();


            if(!is_null($install))
            {
                // If not empty, foreach item in the inventory, compare the item number with the shopify results
                if(count($install->merchant_inventory) > 0)
                {
                    foreach($install->merchant_inventory->toArray() as $x => $local_item)
                    {
                        foreach($results['listings'] as $y => $shopify_item)
                        {
                            if($local_item['platform_id'] == $shopify_item['product_id'])
                            {
                                unset($results['listings'][$y]);
                            }
                        }
                    }
                }
            }
        }

        return $results;
    }

}
