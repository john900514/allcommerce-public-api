<?php

namespace App;

use App\Traits\UuidModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShopifyInstalls extends Model
{
    use UuidModel, SoftDeletes;

    public function merchant()
    {
        return $this->hasOne('App\Merchants', 'uuid', 'merchant_uuid');
    }

    public function merchant_inventory()
    {
        return $this->hasMany('App\MerchantInventory', 'shop_install_id', 'uuid');
    }
}
