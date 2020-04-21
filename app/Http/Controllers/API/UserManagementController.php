<?php

namespace App\Http\Controllers\API;

use App\Actions\Users\GetAllUsers;
use App\User;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

class UserManagementController extends Controller
{
    use Helpers;

    protected $request, $user_model;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index(GetAllUsers $action)
    {
        /**
         * 1. dev-gods can see all users separated by merchant
         * 2. cape & bay non-dev-gods can see cape & bay users by default
         * 3. merchant users can see their default merchant users
         * 4. users assigned to multiple merchants (including cape & bay users) must include the merchant UUID in the header
         * 5. The user must be allowed to see that user's accounts.
         */
        $results = $action->execute();

        if($results)
        {
            $status = 200;
        }
        else
        {
            $results = 'Not Found';
            $status = 404;
        }

        return response($results, $status);
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function show($uuid)
    {

    }

    public function edit($uuid)
    {

    }

    public function update($uuid)
    {

    }

    public function destroy($uuid)
    {

    }
}
