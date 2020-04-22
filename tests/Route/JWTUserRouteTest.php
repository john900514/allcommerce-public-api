<?php

namespace Tests\Route;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tymon\JWTAuth\JWTAuth;

class JWTUserRouteTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        // Important code goes here.
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = User::first();

        auth()->login($user);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        $resp = $this->post( '/api/me',[], $header);

        $this->assertArrayHasKey('token', $resp);
    }


    public function testCapeAndBayUserHasMerchantArrayWithMultipleMerchants()
    {
        //$user = factory(User::class)->create();
        //$user = User::whereIs('dev-god')->first();

        $role_users = [
            $god = User::whereIs('dev-god')->first(),
            $dev = User::whereIs('dev')->first(),
            $user = User::whereIs('platform-user')->first(),
            $muser = User::whereIs('platform-admin')->first(),
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $header = [];
            $header['Accept'] = 'vnd.allcommerce.v1+json';
            $header['Authorization'] = 'Bearer '.$token;

            $resp = $this->post( '/api/me',[], $header);
            $results = json_decode($resp->getContent(), true);

            $this->assertArrayHasKey('merchants', $results);
            $this->assertTrue(is_array($results['merchants']), 'Merchants key is not an array');
            $this->assertTrue(count($results['merchants']) > 1, 'All Cape & Bay Users Should Have Access to All Merchants');
        }
    }


    public function testMerchantUserHasMerchantArrayWithOneMerchant()
    {
        $role_users = [
            $god = User::whereIs('merchant-owner')->first(),
            $dev = User::whereIs('merchant-api-user')->first(),
            $user = User::whereIs('merchant-user')->first(),
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $header = [];
            $header['Accept'] = 'vnd.allcommerce.v1+json';
            $header['Authorization'] = 'Bearer '.$token;

            $resp = $this->post( '/api/me',[], $header);
            $results = json_decode($resp->getContent(), true);

            $this->assertArrayHasKey('merchants', $results);
            $this->assertTrue(is_array($results['merchants']), 'Merchants key is not an array');
            $this->assertTrue(count($results['merchants']) == 1, 'Merchant Users only have access to one merchant account');
        }
    }

    /* @todo - undo when ready to test!
    public function testAMerchantCanHaveMerchantArrayWithMultipleMerchants()
    {
        $role_users = User::whereIs('merchant-admin')->get();
    }
    */

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
