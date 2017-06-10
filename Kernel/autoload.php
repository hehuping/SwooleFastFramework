<?php
/**
 * User: v_hhpphe
 * Date: 2016/12/28
 * Time: 10:34
 */

function autoload($class) {
    //var_dump($class);
    if(strpos($class,'\\') !== false) {
        $classpath = str_replace('\\', '/', $class);
        $filename = dirname(__DIR__)."/$classpath.php";
        if(is_file($filename)) {
           // echo "include success \n";
            include $filename;
        }
    }
}
//注册自动加载函数
spl_autoload_register('autoload');