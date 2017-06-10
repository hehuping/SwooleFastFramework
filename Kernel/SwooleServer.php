<?php

namespace Kernel;

use Lib\PhpRedis;

class SwooleServer{

    protected $config;
    protected $server;
    protected $app;
    protected $route;
    protected $pipeline;
    protected $container;

    public function configure($config){
        $this->config = $config;
        return $this;
    }

    public function run($port){

        $this->server = new \Swoole\Http\Server('0.0.0.0', $port);
        $this->server->set($this->config);


        $this->server->on("Start",function(){
            cli_set_process_title('reload_master');
            echo "Master Worker Start\n";

        });

        /*
         * WorkerStart
         */
        $this->server->on("WorkerStart",function(){
            //加载配置
            Config::load(trim(__DIR__, 'Kernel').'/Config');
            //初始化中间件类
            $this->pipeline = new Pipeline();
            //初始化路由
            $this->route = $route = new \Kernel\Route();
            //初始化请求容器
            $this->container = new \Kernel\Container();
            //加载路由配置
            foreach (glob(Config::get('ROOT_DIR').'/App/Route/*.php') as $router) {
                require_once $router;
            }
            //加载公共方法
            include_once Config::get('ROOT_DIR').'/App/Common/function.php';

            cli_set_process_title('swoole_worker');
            echo "Worker Start\n";

        });

        /**
         * Request Event
         */
        $this->server->on('Request',function(\Swoole\Http\Request $request, \Swoole\Http\Response $response) {

            try{
                ob_start();
                //App业务处理
                $request->appserver = $this->server;
                $this->container->passPara($this->server, $request, $response);
                $app = new App($this->route, $request, $response, $this->container);
                //$app->resolve()->response();
                $this->pipeline->passApp($app, $this->container)->then();

            }catch(\Exception $e){

                $response->end($e->getMessage());

            }
        });

        /*
         * server start
         * */
        $this->server->start();
    }
}
