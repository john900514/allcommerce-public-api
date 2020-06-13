<?php

namespace Tests\Route\Http\Controller;

use App\Merchants;
use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class MerchantControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        // Important code goes here.
    }

    public function testGetMerchant()
    {
        $role_users = [
            $owner = User::whereIs('merchant-owner')->first(),
            $user = User::whereIs('merchant-user')->first()
        ];

        foreach($role_users as $user)
        {
            auth()->login($user);
            $token = auth()->refresh();

            $header['Accept'] = 'vnd.allcommerce.v1+json';
            $header['Authorization'] = 'Bearer '.$token;

            $resp = $this->get( 'http://ac-api.capeandbay.test/api/merchant', $header);
            $result = json_decode($resp->getContent(), true);
            $this->assertTrue(is_array($result));
            $this->assertTrue($result['success']);
            $this->assertArrayHasKey('merchant', $result);


            $resp->assertStatus(200);
        }
    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
