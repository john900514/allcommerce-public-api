<?php

use App\Merchants;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allcommerce = Merchants::firstOrCreate([
            'name' => 'AllCommerce/Cape&Bay',
            'active' => 1
        ]);

        Bouncer::assign('allcommerce')->to($allcommerce);

        switch(env('APP_ENV'))
        {
            case 'prod':
            case 'production':
            case 'staging':
                break;

            default:
                $cannasaurus = Merchants::firstOrCreate([
                    'name' => 'Cannasaurus CBD',
                    'active' => 1
                ]);

                Bouncer::assign('e-commerce-platform')->to($cannasaurus);

                $fakolacrayn = Merchants::firstOrCreate([
                    'name' => 'Faykola Crayon Co',
                    'active' => 1
                ]);

                Bouncer::assign('e-commerce-platform')->to($fakolacrayn);
        }
    }
}
