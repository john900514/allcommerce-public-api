<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class ShopifyInstalls extends Model
{
    use Uuid, SoftDeletes;

    protected $casts = [
        'id' => 'uuid',
        'nonce' => 'uuid',
        'shop_uuid' => 'uuid',
        'merchant_id' => 'uuid',
        'client_id' => 'uuid',
        'logged_in_user' => 'uuid',
    ];

    public function merchant()
    {
        return $this->hasOne('App\Merchants', 'id', 'merchant_id');
    }

    public function merchant_inventory()
    {
        return $this->hasMany('App\MerchantInventory', 'shop_install_id', 'id');
    }

    public function signed_in_user()
    {
        return $this->hasOne('App\User', 'id', 'logged_in_user');
    }
}
