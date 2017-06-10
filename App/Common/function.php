<?php
/**
 * Created by PhpStorm.
 * User: v_hhpphe
 * Date: 2017/1/16
 * Time: 10:16
 */


/**
 * 返回值
 * @param $data
 * @param $message
 * @param $errorCode
 * @param int $httpCode
 * @return array
 */
function responses($data='', $message='', $errorCode=0, $httpCode=200){
    return ['httpCode'=>$httpCode, 'data'=>$data, 'errorCode'=>$errorCode, 'message'=>$message];
}


/**
 * 异步记录访问日志
 * @param string $appname
 */
function asyncRecordLog($request, $appname, $prefix='', $info=''){
    $destination = str_replace('App/Common', 'log', __DIR__).DIRECTORY_SEPARATOR.$prefix.$appname.date('Y_m_d').'.log';
    $log_dir = dirname($destination);
    if(!is_dir($log_dir))
        mkdir($log_dir,0755,true);
    $ip = isset($request->header['x-real-ip']) ? $request->header['x-real-ip'] : 'unknown';
    $user = isset($request->header['staffname']) ? $request->header['staffname'] : 'unknown';
    $file_content = json_encode(['IP'=>$ip, 'REQUEST_TIME' => date('H:i:s',$request->server['request_time']), 'URL'=>$request->server['request_uri'], 'USER'=> $user, 'INFO'=>$info]).",";
    $handle = fopen($destination, 'a');
    fwrite($handle, $file_content);
    fclose($handle);
    /*swoole_async_writefile($destination, $file_content, function($filename) {
    }, FILE_APPEND);*/
}