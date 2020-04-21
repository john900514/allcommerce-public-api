<?php

namespace Tests\Feature;

use App\User;
use App\Merchants;
use Tests\TestCase;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolesTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp() : void
    {
        parent::setUp();
        // Important code goes here.
    }

    public function testCanDevGodControlEverything()
    {
        $god = User::whereIs('dev-god')->first();
        $admin = User::whereIs('platform-admin')->first();
        $user = User::whereIs('platform-user')->first();
        $owner = User::whereIs('merchant-owner')->first();
        $api = User::whereIs('merchant-api-user')->first();
        $madmin = User::whereIs('merchant-admin')->first();
        $muser = User::whereIs('merchant-user')->first();

        $this->assertTrue($god->can('manage', $admin));
        $this->assertTrue($god->can('manage', $user));
        $this->assertTrue($god->can('manage', $owner));
        $this->assertTrue($god->can('manage', $api));
        $this->assertTrue($god->can('manage', $madmin));
        $this->assertTrue($god->can('manage', $muser));
        $this->assertTrue($admin->cannot('manage', $god));
        $this->assertTrue($user->cannot('manage', $god));
        $this->assertTrue($owner->cannot('manage', $god));
        $this->assertTrue($api->cannot('manage', $god));
        $this->assertTrue($madmin->cannot('manage', $god));
        $this->assertTrue($muser->cannot('manage', $god));
    }

    public function testOwnerCannotManageMoreThanOneMerchant()
    {
        $owner = User::whereIs('merchant-owner')->first();
        $default_merchant = $owner->merchant();
        $merchants = Merchants::all();

        foreach($merchants as $merchant)
        {
            if($merchant->uuid == $default_merchant->uuid)
            {
                $this->assertTrue($owner->can('manage', $merchant));
            }
            else
            {
                $this->assertTrue($owner->cannot('manage', $merchant));
            }
        }


    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
