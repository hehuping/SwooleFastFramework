<?php

//demo
$route->group(['namespace' => 'App\Controller'], function() use ($route){
   //hello world
    $route->any('/', '', function(){
		return "hello world";
	});
   
    $route->any('/index2', '', 'Index@index');
   
});




