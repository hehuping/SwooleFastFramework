<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/31
 * Time: 17:46
 */

namespace App\Middleware;
use Closure;

class Auth{
    public static function handle(Closure $next, $container){
        $next();
    }

}