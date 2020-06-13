<?php

use Ramsey\Uuid\Uuid;
use App\ShopifyInstalls;
use Illuminate\Database\Seeder;

class ShopifyInstallsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $dummy1 = ShopifyInstalls::firstOrCreate([
                'nonce' => Uuid::uuid4(),
                'shopify_store_url' => 'wifi-deco.myshopify.com',
                'auth_code' => '123456789',
                'access_token' => '123456789',
                'scopes' => 'write_orders,read_customers',
                'installed' => 1
            ]);

            $dummy2 = ShopifyInstalls::firstOrCreate([
                'nonce' => Uuid::uuid4(),
                'shopify_store_url' => 'angelconcept.myshopify.com',
                'installed' => 0
            ]);

            $dummy3 = ShopifyInstalls::firstOrCreate([
                'nonce' => Uuid::uuid4(),
                'shopify_store_url' => 'touchless-nfc.myshopify.com',
                'auth_code' => '123456789',
                'access_token' => '123456789',
                'scopes' => 'write_orders,read_customers',
                'installed' => 1
            ]);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}
