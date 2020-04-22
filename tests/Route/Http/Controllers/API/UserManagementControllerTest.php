<?php

namespace Tests\Unit\Http\Controllers\API;

use App\User;
use Tests\TestCase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserManagementControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        // Important code goes here.
    }

    public function testIndexNoHeader()
    {
        $role_users = [
            $god = User::whereIs('dev-god')->first(),
            $dev = User::whereIs('dev')->first(),
            $user = User::whereIs('platform-user')->first(),
            $muser = User::whereIs('merchant-user')->first(),

            $admin = User::whereIs('platform-admin')->first(),
            $owner = User::whereIs('merchant-owner')->first(),
            $api = User::whereIs('merchant-api-user')->first(),
            $madmin = User::whereIs('merchant-admin')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $header = [];
            $header['Accept'] = 'vnd.allcommerce.v1+json';
            $header['Authorization'] = 'Bearer '.$token;

            $resp = $this->get( '/api/users', $header);
            $resp->assertStatus(200);

            if(Bouncer::is($user)->a('dev-god', 'dev'))
            {
                // Dev God receives an array with each client name as the key
                // Dev receives an array with cape and bay as the key
                $this->assertTrue(is_array(json_decode($resp->getContent(), true)));

                foreach (json_decode($resp->getContent(), true) as $merchant_name => $merchant_users)
                {
                    foreach ($merchant_users as $merchant_user)
                    {
                        $merchant_user = User::whereUuid($merchant_user['uuid'])->first();
                        $merchant = $merchant_user->merchant();
                        $this->assertTrue($merchant->name == $merchant_name);
                    }
                }
            }
            elseif (Bouncer::is($user)->a('platform-user', 'merchant-user')) {
                // Platform User Receives 1 record, himself.
                // Merchant User Receives 1 record, himself.
                $result = json_decode($resp->getContent(), true);
                $this->assertTrue(is_array(json_decode($resp->getContent(), true)));
                $this->assertTrue(count($result) == 1);
                $this->assertTrue($result[0]['uuid'] == $user->uuid);
            }
            elseif (Bouncer::is($user)->a('merchant-owner', 'merchant-admin','merchant-api-user', 'platform-admin')) {
                // Merchant Owner Receives a list of users from their assigned merchant
                // Merchant Api User Receives a list of users from their assigned merchant
                // Merchant Admin Receives a list of users from their first assigned merchant
                // Platform Admin Receives a list of Cape & Bay users (test user's assigned merchant is cape and bay)
                foreach (json_decode($resp->getContent(), true) as $merchant_user)
                {
                    $default_merchant = $user->merchant();
                    $merchant_user = User::whereUuid($merchant_user['uuid'])->first();
                    $merchant = $merchant_user->merchant();
                    $this->assertTrue($merchant->name == $default_merchant->name);
                }
            }
            else {
                $this->assertStatus(404);
            }
        }
    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }

}
