<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(MerchantSeeder::class);
        $this->call(DefaultUsersSeeder::class);

        if((env('APP_ENV') == 'testing') || (env('APP_ENV') == 'pipelines'))
        {
            $this->call(ShopifyInstallsSeeder::class);
        }
    }
}
