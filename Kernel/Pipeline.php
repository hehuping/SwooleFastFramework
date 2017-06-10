<?php
/**
 * Created by PhpStorm.
 * Date: 2016/12/31
 * Time: 17:36
 */
namespace Kernel;
class Pipeline{

    public $app = null;
    public $request;
    public $response;
    public $result = null;
    public $container;

    public  function getSlice() {

        return function($stack,$pipe) {
            return function() use($stack,$pipe){
                return $pipe::handle($stack, $this->container);
            };
        };

    }

    public function passApp($app, $container){

        $this->app = $app;
        $this->container = $container;
        return $this;

    }

    public function then() {
        if(!$this->app->checkHost() || !$this->app->checkUri() || !$this->app->checkMethod()){
            return $this->app->response();
        }

        $pipe = $this->app->getCurrentMiddleware();
        $firstSlice = function() {
            $this->result =  $this->app->resolve()->response();
        };

        $pipe = array_reverse($pipe);
        $callback = array_reduce($pipe,$this->getSlice(),$firstSlice);
        $callback();
        return $this->result;
    }
}