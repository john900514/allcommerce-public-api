<?php

namespace App\Actions\Merchants\Inventory;

use App\Merchants;
use App\MerchantInventory;
use Illuminate\Support\Facades\Log;
use Silber\Bouncer\BouncerFacade as Bouncer;

class GetAllInventory
{
    protected $merchants, $merch;

    public function __construct(Merchants $m, MerchantInventory $i)
    {
        $this->merchants = $m;
        $this->merch = $i;
    }

    public function execute($merchant_uuid)
    {
        $results = false;

        // Locate the Merchant record or fail
        $merchant = $this->merchants->whereUuid($merchant_uuid)->first();

        if(!is_null($merchant) && !Bouncer::is($merchant)->an('allcommerce'))
        {
            // Locate the auth'd user's merchant or fail
            $user = auth()->user();
            $users_merchant = $user->merchant();

            if(!is_null($user) && !is_null($users_merchant))
            {
                // If the user's merchant is allcommerce, continue
                // or if the user can view the merchant continue.
                if(Bouncer::is($users_merchant)->an('allcommerce') || $user->can('view', $merchant))
                {
                    // Get all the inventory for the user
                    $results = $this->getAllInventory($merchant);
                }
            }
        }

        return $results;
    }

    private function getAllInventory(Merchants $merchant)
    {
        $results = [];

        $records = $merchant->inventory()->get();

        if(count($records) > 0)
        {
            /**
             * STEPS - @todo - curate the shit out of this!
             * @todo - Get Variants
             * @todo - Get Variant Options
             * @todo - Get Inventory Images
             * @todo - Get Inventory Variant Images
             * @todo - Get Limited Merchant Info
             */
            $results = $records->toArray();

            /*
         // Get merchant_inventory record
            $inventory = $merchant->inventory()->get();

            if(count($inventory) > 0)
            {
                $payload = [
                    'inventory' => []
                ];
                foreach($inventory as $product)
                {
                    $data = $product->toArray();
                    // Get inventory variants
                    $variants = $product->variants()->get();

                    // Get variant options
                    $variant_option = $product->variant_options()->get();

                    // Curate and return
                    $data['variants'] = $variants->toArray();
                    $data['variant_options'] = $variant_option->toArray();
                    $payload['inventory'][] = $data;
                }

                $payload['merchant'] = $merchant->toArray();
                $payload['success'] = true;
                $results = $payload;
            }
         */
        }

        return $results;
    }
}
