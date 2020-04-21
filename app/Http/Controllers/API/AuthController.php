<?php

namespace App\Http\Controllers\API;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    use Helpers;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return ['token' => $token];
    }

    /**
     * Get the authenticated User.
     *
     */
    public function me()
    {
        $results = [
            'user' => auth()->user(),
            'token' => auth()->refresh(),
            'roles' => auth()->user()->getRoles()
        ];
        return response()->json($results);
    }
}
