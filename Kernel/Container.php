<?php
/**
 * Created by PhpStorm.
 * User: v_hhpphe
 * Date: 2017/3/14
 * Time: 15:05
 * Request Response SwooleServer 对象容器类
 */

namespace Kernel;


class Container
{
    public $request;
    public $response;
    public $swoole_server;

    public function passPara($swoole_server, $request, $response)
    {
        $this->swoole_server = $swoole_server;
        $this->request = $request;
        $this->response = $response;
        return $this;
    }

    /**
     * 先获取get再获取post
     * @param string $name
     * @return array|string
     */
    public function getPost($name=''){
        if(!empty($name)){
            if(isset($this->request->get) && isset($this->request->get[$name])){
                return $this->request->get[$name];
            }elseif(isset($this->request->post) && isset($this->request->post[$name])){
                return $this->request->post[$name];
            }else{
                return '';
            }
        }else{
            if(isset($this->request->get) && isset($this->request->post)){
                return array_merge($this->request->get, $this->request->get);
            }elseif(isset($this->request->get)){
                return $this->request->get;
            }elseif(isset($this->request->post)){
                return $this->request->post;
            }else{
                return [];
            }
        }
    }

    /**
     * @param string $key
     * @param string $default
     * @return array|string
     */
    public function getHeader($key='', $default=''){
        if(!empty($key)){
            if(isset($this->request->header) && isset($this->request->header[$key])){
                return $this->request->header[$key];
            }else{
                return $default;
            }
        }else{
            return isset($this->request->header) ? $this->request->header : '';
        }
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key=''){
        if(!empty($key)){
            if(isset($this->request->get) && isset($this->request->get[$key])){
                return $this->request->get[$key];
            }else{
                return '';
            }
        }else{
            return isset($this->request->get) ? $this->request->get : '';
        }
    }

    /**
     * 获取post中的数据
     * @param $name
     * @return array
     */
    public function post($key=''){
        if(!empty($key)){
            if(isset($this->request->post) && isset($this->request->post[$key])){
                return $this->request->post[$key];
            }else{
                return '';
            }
        }else{
            return isset($this->request->post) ? $this->request->post : '';
        }
    }

}