<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'shop_name',
        'shop_category_id',
        'brand',
        'on_time',
        'fengniao',
        'bao',
        'piao',
        'zhun',
        'shop_img',
        'status',
    ];

}
