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
}
