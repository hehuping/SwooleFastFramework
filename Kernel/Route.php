<?php
/**
 * User: v_hhpphe
 * Date: 2016/12/28
 * Time: 10:59
 */

namespace Kernel;
use Closure;

class Route
{
    public $pathMatch = []; //路由匹配规则
    public $pathList = [];
    protected $groupAttributes = [];

    /**
     * 路由信息获取
     * @param $httpMethod
     * @param $url
     * @param $middleware
     * @param $dispatcher
     */
    public function addRoute($httpMethod, $uri, $middleware, $dispatcher){

        //分组中间件
        if (isset($this->groupAttributes['middleware'])) {
            $middleware ? array_push($this->groupAttributes['middleware'], $middleware) : '';
            $middleware = $this->groupAttributes['middleware'];
        }

        //Url prefix前缀添加
        if(isset($this->groupAttributes['prefix']) && is_string($this->groupAttributes['prefix'])){
            $uri = $this->groupAttributes['prefix'].$uri;
        }


        //命名空间与中间件处理
        $namespace = isset($this->groupAttributes['namespace']) ? $this->groupAttributes['namespace'] : '';
        $middleware = is_array($middleware) ? $middleware : (empty($middleware) ? [] : array($middleware));
        $host = isset($this->groupAttributes['host']) ? $this->groupAttributes['host'] : '';

        //中间件命名空间注册
        $middleware = array_map(function($value){
            return 'App\Middleware\\'.$value;
            //return $this->groupAttributes['namespace'].'\\'.$value;
        }, $middleware);

        $routeInfo = ['method'=>(array) $httpMethod,  'middleware'=>$middleware, 'namespace'=>$namespace, 'use'=>$dispatcher, 'host'=>$host];
        $this->pathMatch[$uri] = $routeInfo;
        if(!in_array($uri, $this->pathList)){
            array_push($this->pathList, $uri);
        }
    }

    /**
     * 路由分组
     * @param array $attributes
     * @param Closure $callback
     */
    public function group(array $attributes, Closure $callback){

        $parentGroupAttributes = $this->groupAttributes;
        if (isset($attributes['middleware']) && is_string($attributes['middleware']))
            $attributes['middleware'] = explode('|', $attributes['middleware']);

        $this->groupAttributes = $attributes;
        call_user_func($callback);
        $this->groupAttributes = $parentGroupAttributes;
    }

    /**
     * @param string $path
     * @param $middleware
     * @param $dispatcher
     */
    public function get($path='/', $middleware, $dispatcher){
        $this->addRoute('GET', $path, $middleware, $dispatcher);
    }

    public function post($path='/', $middleware, $dispatcher){
        $this->addRoute('POST', $path, $middleware, $dispatcher);
    }

    public function any($path='/', $middleware, $dispatcher){
        $this->addRoute(array('GET', 'POST'), $path, $middleware, $dispatcher);
    }
}