<?php

$options = getopt("s:");
$options = empty($options) ? '' : $options['s'];

require __DIR__.'/Kernel/autoload.php';

$pidFile = __DIR__.'/server.pid';

$pid = null;
if(file_exists($pidFile)){
    $pid = file_get_contents($pidFile);
}


if($pid && $options){
    switch($options){
        //reload worker
        case 'reload':
            exec('kill -USR1 '.$pid);
            echo "reload success ! \n";
            break;
        case 'stop':
            //kill -SIGTERM is doesn't work
            exec('kill -15 '.$pid);
            echo "stop service !\n";
            break;
        default:
            echo "No no such pid file \n";
    }
}else{

    $server = new \Kernel\SwooleServer();
    $server->configure([
        'worker_num' => 8,
        'daemonize' => 1,
        'max_request' => 100000,
        'max_conn' => 10000,
        'dispatch_mode' => 3,
        'log_file' => './log/swoole.log',
        'pid_file' => $pidFile,
        'open_tcp_nodelay' => true,
        'buffer_output_size' => 128 * 1024 *1024, //必须为数字

    ])->run(9502);
}
