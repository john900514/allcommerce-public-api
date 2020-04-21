<?php

namespace Tests\Unit\Http\Controllers\API;

use App\Merchants;
use App\User;
use Tests\TestCase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        // Important code goes here.
    }

    public function testCapeAndBayUsersIndexNoHeader()
    {
        // Cape & Bay Merchant doesn't have inventory so return unauthorized for all types including dev-god
        $role_users = [
            $god = User::whereIs('dev-god')->first(),
            $dev = User::whereIs('dev')->first(),
            $user = User::whereIs('platform-user')->first(),
            $admin = User::whereIs('platform-admin')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $header = [];
            $header['Accept'] = 'vnd.allcommerce.v1+json';
            $header['Authorization'] = 'Bearer '.$token;

            $resp = $this->get( 'http://ac-api.capeandbay.test/api/inventory', $header);
            $resp->assertStatus(401);
        }
    }

    public function testCapeAndBayUsersIndexWithHeaderI()
    {
        $role_users = [
            $god = User::whereIs('dev-god')->first(),
            $dev = User::whereIs('dev')->first(),
            $user = User::whereIs('platform-user')->first(),
            $admin = User::whereIs('platform-admin')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $merchants = Merchants::all();

            foreach($merchants as $req_merchant)
            {
                if(Bouncer::is($req_merchant)->an('allcommerce'))
                {
                    // AllCommerce doesn't have merch dammit!
                    $altheader = [];
                    $altheader['Accept'] = 'vnd.allcommerce.v1+json';
                    $altheader['Authorization'] = 'Bearer '.$token;
                    $altheader['merchant-uuid'] = $req_merchant->uuid;

                    $resp = $this->get( 'http://ac-api.capeandbay.test/api/inventory', $altheader);

                    $resp->assertStatus(405);
                    $req_merchant = null;
                }
            }
        }

    }

    public function testCapeAndBayUsersIndexWithHeaderII()
    {
        $role_users = [
            $god = User::whereIs('dev-god')->first(),
            $dev = User::whereIs('dev')->first(),
            $user = User::whereIs('platform-user')->first(),
            $admin = User::whereIs('platform-admin')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $merchants = Merchants::all();

            foreach($merchants as $req_merchant)
            {
                if(!Bouncer::is($req_merchant)->an('allcommerce'))
                {
                    // AllCommerce doesn't have merch dammit!
                    // All Cape & Bay Users should get an array
                    $header = [];

                    $merchant_id = $req_merchant->uuid;

                    $header['Accept'] = 'vnd.allcommerce.v1+json';
                    $header['Authorization'] = 'Bearer '.$token;
                    $header['merchant-uuid'] = $merchant_id;

                    $resp = $this->get( 'http://ac-api.capeandbay.test/api/inventory', $header);

                    $resp->assertStatus(200);
                    $result = json_decode($resp->getContent(), true);
                    $this->assertTrue(is_array($result));
                    break;
                }
            }
        }
    }

    public function testMerchantUsersIndexWithHeadersI()
    {
        $role_users = [
            $owner = User::whereIs('merchant-owner')->first(),
            $api = User::whereIs('merchant-api-user')->first(),
            $madmin = User::whereIs('merchant-admin')->first(),
            $muser = User::whereIs('merchant-user')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();
            $users_merchant = $user->merchant();

            $merchants = Merchants::all();

            foreach($merchants as $req_merchant)
            {
                if($req_merchant->uuid != $users_merchant->uuid)
                {
                    if(!Bouncer::is($req_merchant)->an('allcommerce'))
                    {
                        // No access outside a user's merchant account!
                        $altheader = [];
                        $altheader['Accept'] = 'vnd.allcommerce.v1+json';
                        $altheader['Authorization'] = 'Bearer '.$token;
                        $altheader['merchant-uuid'] = $req_merchant->uuid;

                        $resp = $this->get( 'http://ac-api.capeandbay.test/api/inventory', $altheader);

                        $resp->assertStatus(404);
                        $req_merchant = null;
                    }
                }
            }
        }

    }

    public function testMerchantUsersIndexWithHeadersII() {
        $role_users = [
            $owner = User::whereIs('merchant-owner')->first(),
            $api = User::whereIs('merchant-api-user')->first(),
            $madmin = User::whereIs('merchant-admin')->first(),
            $muser = User::whereIs('merchant-user')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();
            $users_merchant = $user->merchant();

            $merchants = Merchants::all();

            foreach($merchants as $req_merchant)
            {
                if($req_merchant->uuid == $users_merchant->uuid)
                {
                    if(!Bouncer::is($req_merchant)->an('allcommerce'))
                    {
                        // No access outside a user's merchant account!
                        $altheader = [];
                        $altheader['Accept'] = 'vnd.allcommerce.v1+json';
                        $altheader['Authorization'] = 'Bearer '.$token;
                        $altheader['merchant-uuid'] = $req_merchant->uuid;

                        $resp = $this->get( 'http://ac-api.capeandbay.test/api/inventory', $altheader);

                        $resp->assertStatus(200);
                        $result = json_decode($resp->getContent(), true);
                        $this->assertTrue(is_array($result));
                    }
                }
            }

        }
    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
