<?php

use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dev_god = Bouncer::role()->firstOrCreate(
            [
                'name' => 'dev-god',
                'title' => 'Dev God',
            ]
        );

        $dev = Bouncer::role()->firstOrCreate(
            [
                'name' => 'dev',
                'title' => 'AllCommerce Developer',
            ]
        );

        $platform_admin = Bouncer::role()->firstOrCreate(
            [
                'name' => 'platform-admin',
                'title' => 'AllCommerce Admin',
            ]
        );

        $platform_user = Bouncer::role()->firstOrCreate(
            [
                'name' => 'platform-user',
                'title' => 'AllCommerce Non-Admin, Non-Merchant User',
            ]
        );

        $allcommerce = Bouncer::role()->firstOrCreate(
            [
                'name' => 'allcommerce',
                'title' => 'AllCommerce Platform',
            ]
        );

        $merchant_owner = Bouncer::role()->firstOrCreate(
            [
                'name' => 'merchant-owner',
                'title' => 'Client Owner',
            ]
        );

        $merchant_api_user = Bouncer::role()->firstOrCreate(
            [
                'name' => 'merchant-api-user',
                'title' => 'Client API User',
            ]
        );

        $merchant_admin = Bouncer::role()->firstOrCreate(
            [
                'name' => 'merchant-admin',
                'title' => 'Client Admin',
            ]
        );

        $merchant_user = Bouncer::role()->firstOrCreate(
            [
                'name' => 'merchant-user',
                'title' => 'Client Non-Admin',
            ]
        );

        $ecommerce_platform = Bouncer::role()->firstOrCreate(
            [
                'name' => 'e-commerce-platform',
                'title' => 'eCommerce Platform',
            ]
        );

        Bouncer::allow($dev_god)->everything();


        Bouncer::allow($dev)->toManage($platform_admin);
        Bouncer::allow($dev)->toManage($platform_user);
        Bouncer::allow($dev)->toManage($allcommerce);
        Bouncer::allow($dev)->toManage($merchant_owner);
        Bouncer::allow($dev)->toManage($merchant_api_user);
        Bouncer::allow($dev)->toManage($merchant_admin);
        Bouncer::allow($dev)->toManage($merchant_user);
        Bouncer::allow($dev)->toManage($ecommerce_platform);

        Bouncer::allow($platform_admin)->toManage($dev);
        Bouncer::allow($platform_admin)->toManage($platform_user);
        Bouncer::allow($platform_admin)->toManage($merchant_owner);
        Bouncer::allow($platform_admin)->toManage($merchant_api_user);
        Bouncer::allow($platform_admin)->toManage($merchant_admin);
        Bouncer::allow($platform_admin)->toManage($merchant_user);

        Bouncer::allow($allcommerce)->toManage($merchant_admin);
        Bouncer::allow($allcommerce)->toManage($merchant_user);
        Bouncer::allow($allcommerce)->toManage($merchant_owner);
        Bouncer::allow($allcommerce)->toManage($merchant_api_user);
        Bouncer::allow($allcommerce)->toManage($merchant_admin);
        Bouncer::allow($allcommerce)->toManage($merchant_user);
        Bouncer::allow($allcommerce)->toManage($ecommerce_platform);

        Bouncer::allow($merchant_owner)->toManage($merchant_user);
        Bouncer::allow($merchant_owner)->toManage($merchant_api_user);
        Bouncer::allow($merchant_owner)->toManage($merchant_admin);

        Bouncer::allow($merchant_api_user)->toManage($merchant_admin);
        Bouncer::allow($merchant_api_user)->toManage($merchant_user);
        Bouncer::allow($merchant_api_user)->toManage($ecommerce_platform);

        Bouncer::allow($merchant_admin)->toManage($merchant_user);
        Bouncer::allow($merchant_admin)->to('be-assigned-to-multiple-merchants');

        Bouncer::allow($ecommerce_platform)->toManage($merchant_admin);
        Bouncer::allow($ecommerce_platform)->toManage($merchant_user);



    }
}
