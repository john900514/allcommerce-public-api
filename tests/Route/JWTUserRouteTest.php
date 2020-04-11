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
        $user = factory(User::class)->create();

        auth()->login($user);
        $token = auth()->refresh();

        $header = [];
        $header['Accept'] = 'application/json';
        $header['Authorization'] = 'Bearer '.$token;

        $resp = $this->json('POST', '/api/me',[], $header);
        $this->assertArrayHasKey('token', $resp);
    }

    public function tearDown() : void
    {
        parent::tearDown();
        // Important code goes here.
    }
}
