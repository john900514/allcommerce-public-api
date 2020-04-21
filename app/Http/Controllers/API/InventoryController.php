<?php

namespace App\Http\Controllers\API;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Actions\Merchants\Inventory\GetAllInventory;

class InventoryController extends Controller
{
    use Helpers;

    protected $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * GET /inventory
     */
    public function index(GetAllInventory $action)
    {
        $results = 'Unauthorized';
        $status = 401;

        $user = auth()->user();
        $merchant = $user->merchant();

        if(!Bouncer::is($merchant)->an('allcommerce'))
        {
            $header_uuid = $this->request->header('merchant-uuid');
            $access_merchant_uuid = (!is_null($header_uuid)) ? $header_uuid : $merchant->uuid;

            $results = $action->execute($access_merchant_uuid);

            if(is_array($results))
            {
                $status = 200;
            }
            else
            {
                $results = 'Not Found';
                $status = 404;
            }
        }
        else
        {
            $access_merchant_uuid = $this->request->header('merchant_uuid');

            if(!is_null($access_merchant_uuid))
            {
                $results = $action->execute($access_merchant_uuid);

                if(is_array($results))
                {
                    $status = 200;
                }
                else
                {
                    $results = 'Invalid Merchant';
                    $status = 405;
                }
            }
        }

        return response($results,$status);
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
