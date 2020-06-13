<?php

namespace Tests\Route\Http\Controller;

use App\User;
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

    public function testLinkToShopifySuccessNew()
    {
        // Sign in as Cat
        $cat = User::whereEmail('cat@capeandbay.com')->first();
        auth()->login($cat);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to faykola to dummy-store
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'wifi-deco.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertTrue($result['success']);
    }

    public function testLinkToShopifySuccessFromAlreadyBeingAssigned()
    {
        // Sign in as Cat
        $this->testLinkToShopifySuccessNew();
        $cat = User::whereEmail('cat@capeandbay.com')->first();
        auth()->login($cat);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to faykola to dummy-store
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'wifi-deco.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertTrue($result['success']);
    }

    public function testLinkToShopifyFailFromLinkToAnotherMerchant()
    {
        // Sign in as Ross
        $this->testLinkToShopifySuccessNew();
        $ross = User::whereEmail('ross@placeholder.com')->first();
        auth()->login($ross);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to faykola to dummy-store
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'wifi-deco.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('reason', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertTrue($result['reason'] == 'Error - Shop Assigned to Another Merchant.');
    }

    public function testLinkToShopifyFailBecauseAppIsNotInstalled()
    {
        // Sign in as Ross
        $ross = User::whereEmail('ross@placeholder.com')->first();
        auth()->login($ross);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to CBD to dummy-store2
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'angelconcept.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('reason', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertTrue($result['reason'] == 'Error - Shop Not Installed');
    }

    public function testLinkToShopifyFailFromShopNotExist()
    {
        // Sign in as Cat

        $cat = User::whereEmail('cat@capeandbay.com')->first();
        auth()->login($cat);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to faykola to dummy-store
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'fukyer-face.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('reason', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertTrue($result['reason'] == 'Error - Invalid Shopify Shop');
    }

    public function testLinkToShopifyFailFromMissingShopParameter()
    {
        // Sign in as Cat

        $cat = User::whereEmail('cat@capeandbay.com')->first();
        auth()->login($cat);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to faykola to dummy-store
        $resp = $this->post( '/api/shopify/merchant/assign', [],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('reason', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertTrue($result['reason'] == 'Error - Missing Shopify Shop');
    }

    public function testLinkToShopifyFailFromPermissions()
    {
        // Sign in as Rhys
        $rhys = User::whereEmail('rhys@placeholder.com')->first();
        auth()->login($rhys);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'vnd.allcommerce.v1+json';
        $header['Authorization'] = 'Bearer '.$token;

        // Have route link to CBD to dummy-store3
        $resp = $this->post( '/api/shopify/merchant/assign', ['shop' => 'touchless-nfc.myshopify.com'],$header);
        $result = json_decode($resp->getContent(), true);
        $this->assertTrue(is_array($result));
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('reason', $result);
        $this->assertArrayHasKey('msg', $result);
        $this->assertTrue($result['reason'] == 'Error - Invalid Permissions');

    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
