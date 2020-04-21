<?php

use App\User;
use App\Merchants;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Services\Merchants\UserMerchantService;

class DefaultUsersSeeder extends Seeder
{
    protected  $merchant_users;
    public function __construct()
    {

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_pw = bcrypt('Hello123!');
        $capeandbay = Merchants::whereName('AllCommerce/Cape&Bay')->first();

        $devs = User::whereEmail('developers@capeandbay.com')->first();
        if(is_null($devs))
        {
            $devs = User::firstOrCreate([
                'name' => 'Cape&Bay Admin',
                'email' => 'developers@capeandbay.com',
                'password' => $default_pw
            ]);
        }
        Bouncer::assign('dev-god')->to($devs);
        UserMerchantService::assign($devs)->to($capeandbay);


        $angel = User::whereEmail('angel@capeandbay.com')->first();
        if(is_null($angel))
        {
            $angel = User::firstOrCreate([
                'name' => 'Angel Gonzalez',
                'email' => 'angel@capeandbay.com',
                'password' => $default_pw
            ]);
        }
        Bouncer::assign('dev')->to($angel);
        UserMerchantService::assign($angel)->to($capeandbay);

        $tareq = User::whereEmail('tareq@capeandbay.com')->first();
        if(is_null($tareq))
        {
            $tareq = User::firstOrCreate([
                'name' => 'Tareq Othman',
                'email' => 'tareq@capeandbay.com',
                'password' => $default_pw
            ]);
        }
        Bouncer::assign('platform-admin')->to($tareq);
        UserMerchantService::assign($tareq)->to($capeandbay);

        $gedy = User::whereEmail('gedy@capeandbay.com')->first();
        if(is_null($gedy))
        {
            $gedy = User::firstOrCreate([
                'name' => 'Gedy Leon',
                'email' => 'gedy@capeandbay.com',
                'password' => $default_pw
            ]);
        }
        Bouncer::assign('platform-user')->to($gedy);
        UserMerchantService::assign($gedy)->to($capeandbay);

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

        switch(env('APP_ENV'))
        {
            case 'prod':
            case 'production':
            case 'staging':
                break;

            default:
                $fakolacrayn = Merchants::whereName('Faykola Crayon Co')->first();

                $cat = User::whereEmail('cat@capeandbay.com')->first();
                if(is_null($cat))
                {
                    $cat = User::firstOrCreate([
                        'name' => 'Cat Denning',
                        'email' => 'cat@capeandbay.com',
                        'password' => $default_pw
                    ]);
                }

                Bouncer::assign('merchant-owner')->to($cat);
                Bouncer::allow($cat)->toManage($fakolacrayn);
                UserMerchantService::assign($cat)->to($fakolacrayn);

                // Introducing the Placeholders

                $chris = User::whereEmail('acg0715@gmail.com')->first();
                if(is_null($chris))
                {
                    $chris = User::firstOrCreate([
                        'name' => 'Chris Placeholder',
                        'email' => 'acg0715@gmail.com',
                        'password' => $default_pw
                    ]);
                }

                Bouncer::assign('merchant-api-user')->to($chris);
                //Bouncer::allow($chris)->toOwn($fakolacrayn)->to(['access_oauth']);
                UserMerchantService::assign($chris)->to($fakolacrayn);

                $barb = User::whereEmail('barb@placeholder.com')->first();
                if(is_null($barb))
                {
                    $barb = User::firstOrCreate([
                        'name' => 'Barbara Placeholder',
                        'email' => 'barb@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                Bouncer::assign('merchant-admin', $fakolacrayn)->to($barb);
                //Bouncer::allow($barb)->toOwn($fakolacrayn)->to(['update']);
                UserMerchantService::assign($barb)->to($fakolacrayn);

                $charles = User::whereEmail('charles@placeholder.com')->first();
                if(is_null($charles))
                {
                    $charles = User::firstOrCreate([
                        'name' => 'Charles Placeholder',
                        'email' => 'charles@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                Bouncer::assign('merchant-user', $fakolacrayn)->to($charles);
                UserMerchantService::assign($charles)->to($fakolacrayn);


                $cannasaurus = Merchants::firstOrCreate([
                    'name' => 'Cannasaurus CBD',
                    'active' => 1
                ]);

                $ross = User::whereEmail('ross@placeholder.com')->first();
                if(is_null($ross))
                {
                    $ross = User::firstOrCreate([
                        'name' => 'Ross Placeholder',
                        'email' => 'ross@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                $zoe = User::whereEmail('zoe@placeholder.com')->first();
                if(is_null($zoe))
                {
                    $zoe = User::firstOrCreate([
                        'name' => 'Zoe Placeholder',
                        'email' => 'zoe@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                $candace = User::whereEmail('candace@placeholder.com')->first();
                if(is_null($candace))
                {
                    $candace = User::firstOrCreate([
                        'name' => 'Candace Placeholder',
                        'email' => 'candace@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                $rhys = User::whereEmail('rhys@placeholder.com')->first();
                if(is_null($rhys))
                {
                    $rhys = User::firstOrCreate([
                        'name' => 'Rhys Placeholder',
                        'email' => 'rhys@placeholder.com',
                        'password' => $default_pw
                    ]);
                }

                Bouncer::assign('merchant-owner', $cannasaurus)->to($ross);
                Bouncer::allow($cat)->toManage($fakolacrayn);
                Bouncer::assign('merchant-api-user', $cannasaurus)->to($zoe);
                Bouncer::assign('merchant-admin', $cannasaurus)->to($candace);
                Bouncer::assign('merchant-user', $cannasaurus)->to($rhys);
                UserMerchantService::assign($ross)->to($cannasaurus);
                UserMerchantService::assign($zoe)->to($cannasaurus);
                UserMerchantService::assign($candace)->to($cannasaurus);
                UserMerchantService::assign($rhys)->to($cannasaurus);
        }
    }
}
