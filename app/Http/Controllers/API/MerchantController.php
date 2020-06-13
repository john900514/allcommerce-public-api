<?php

namespace App\Http\Controllers\API;

use App\Merchants;
use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

class MerchantController extends Controller
{
    use Helpers;

    protected $merchants, $request;

    public function __construct(Request $request, Merchants $merchants)
    {
        $this->request = $request;
        $this->merchants = $merchants;
    }

    public function index()
    {
        $results = ['success' => false, 'results' => 'Invalid User'];

        $user = auth()->user();

        if(!is_null($user))
        {
            // @todo - if header has a different merchant_uuid and the user is a merchant-admin
            // @todo - query for that merchant instead or deny access.

            $merchant = $user->merchant()
                ->with('shopify_installs')
                ->first();

            if(!is_null($merchant))
            {
                $payload = $merchant->toArray();
                // @todo - add other related merchant data
                $results = ['success' => true, 'merchant' => $merchant];
            }
            else
            {
                $results['reason'] = 'Could not locate merchant!';
            }
        }

        return $results;
    }

    public function link_to_shopify()
    {
        $results = ['success' => false];

        return $results;
    }
}
