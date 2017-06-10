<?php
/**
 * User: v_hhpphe
 * Date: 2016/12/28
 * Time: 10:40
 */
namespace Kernel;
use App\Middleware;
class App{

    private $route;
    private $result = [
        'errorCode' => 0,
        'message' => '',
        'data' => '',
        'httpCode' => 200
    ];
    protected $response;
    protected $request;
    protected $container;

    public function __construct($route, $request, $response, $container){
        $this->response = $response;
        $this->request = $request;
        $this->route = $route;
        $this->container = $container;
    }

    /**
     * 路由解析开始
     * Route resolve
     */
    public function resolve(){
        //Check uil and method
       // if($this->checkMethod()){
            if(is_callable($this->route->pathMatch[$this->request->server['request_uri']]['use'])){
                $this->result = call_user_func($this->route->pathMatch[$this->request->server['request_uri']]['use'], $this->request, $this->response);
            }else{
                //Run Controller
                $this->runController();
            }
      //  }
        return $this;
    }

    /**
     * 检查请求方法是否合法
     * Check http method
     */
    public function checkMethod(){

        if(!in_array(strtoupper($this->request->server['request_method']), $this->route->pathMatch[$this->request->server['request_uri']]['method'])){
            $this->result['errorCode'] = 2;
            $this->result['httpCode'] = 502;
            $this->result['message'] = 'Illegal request method';
            return false;
        }
        return true;

    }

    /**
     * 检查是否注册uri
     * Check http request uri
     */
    public function checkUri(){
        if(!in_array($this->request->server['request_uri'], $this->route->pathList)){
           $this->result['errorCode'] = 1;
           $this->result['httpCode'] = 501;
            $this->result['message'] = 'Unregistered request';
            $this->result['url'] = $this->request->server['request_uri'];
            asyncRecordLog($this->request, '', 'request_error');
            return false;
        }
        return true;
    }

    /**
     * 控制器运行
     * Run Controller
     */
    protected function runController(){
        //get controller
        $controller = explode('@', $this->route->pathMatch[$this->request->server['request_uri']]['use']);
        //get action
        $action = isset($controller[1]) ? $controller[1] : 'index';
        //get namespace
        $namespace = empty($this->route->pathMatch[$this->request->server['request_uri']]['namespace']) ? 'App\\Controller': $this->route->pathMatch[$this->request->server['request_uri']]['namespace'];
        $controller =  $namespace.'\\'.$controller[0];

        $this->result['errorCode'] = 4;
        $this->result['httpCode'] = 504;
        $this->result['message'] = "Controller Not Found";
        if(class_exists($controller)){
            //container is http request and response
            $class = new $controller($this->container);
            $this->result['message']  = "Action Not Found";
            if(method_exists($class, $action)){
                $this->result['errorCode'] = 0;
                $this->result['httpCode'] = 200;
                $this->result = $class->$action($this->response);
            }
        }
    }

    /**
     * 获取当前路由中间件
     * Get Current middleware
     */
    public function getCurrentMiddleware(){
        return  isset($this->route->pathMatch[$this->request->server['request_uri']]['middleware']) ? $this->route->pathMatch[$this->request->server['request_uri']]['middleware'] : [];
    }

    /**
     * 判断当前host
     * check Host
     */
    public function checkHost(){
        if(!empty($this->route->pathMatch[$this->request->server['request_uri']]['host']) && $this->route->pathMatch[$this->request->server['request_uri']]['host'] != $this->request->header['x-real-host']){
            $this->result['errorCode'] = 3;
            $this->result['httpCode'] = 404;
            $this->result['message'] = 'Illegal host';
            return false;
        }
        return true;
    }

    /**
     * 统一返回
     * Response catch
     */
    public function response(){

        $content = ob_get_contents();
        ob_end_clean();

        if(is_array($this->result) || is_object($this->result)){

            if(is_array($this->result))
                isset($this->result['httpCode']) ? $this->response->status($this->result['httpCode']) : '';

            $this->response->write(json_encode($this->result));
            $this->response->end();

        }else{

            empty($content) ? '' : ($this->response->write($content) && $this->response->end()) ;
            empty($this->result) ? '' : ($this->response->write($this->result) && $this->response->end());
        }



    }
}