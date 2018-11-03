<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [ 'user/login','user/list','shop/list','shop/shopList','shop/sendSms','user/regist','shop/addressList','shop/addAddress','shop/address','shop/editAddress','shop/cart','shop/addCart','shop/addorder','shop/order','shop/orderList','shop/changePassword','shop/forgetPassword'
        //排除不需要csrf_token验证的路由

    ];
}
