<?php

namespace App\Http\Controllers\API;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;

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
    public function index()
    {
        $results = ['success' => false];

        $user = auth()->user();
        $merchant = $user->merchant();

        // Get merchant_inventory record
        $inventory = $merchant->inventory()->get();

        if(count($inventory) > 0)
        {
            $payload = [
                'inventory' => []
            ];
            foreach($inventory as $product)
            {
                $data = $product->toArray();
                // Get inventory variants
                $variants = $product->variants()->get();

                // Get variant options
                $variant_option = $product->variant_options()->get();

                // Curate and return
                $data['variants'] = $variants->toArray();
                $data['variant_options'] = $variant_option->toArray();
                $payload['inventory'][] = $data;
            }

            $payload['merchant'] = $merchant->toArray();
            $payload['success'] = true;
            $results = $payload;
        }

        return response()->json($results);
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
