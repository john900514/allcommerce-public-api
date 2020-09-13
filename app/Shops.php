<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Shops extends Model
{
    use SoftDeletes, Uuid;

    protected $guarded = [];

    protected $casts = [
        'id' => 'uuid',
        'merchant_id' => 'uuid',
        'client_id' => 'uuid',
        'shop_type' => 'uuid'
    ];

    public function merchant()
    {
        return $this->belongsTo('AnchorCMS\Merchants', 'merchant_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo('AnchorCMS\Clients', 'client_id', 'id');
    }

    public function shop_type()
    {
        return $this->belongsTo('AnchorCMS\ShopTypes', 'shop_type', 'id');
    }

    public function shoptype()
    {
        return $this->shop_type();
    }
}
