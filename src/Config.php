<?php
return [
    'host' => '0.0.0.0',
    'port' => 9501,
    'sock_type' => SWOOLE_SOCK_TCP,
    'set' => [
        'worker_num' => swoole_cpu_num()*2,
        'max_request' => 1000,
        'daemonize' => false,
        'log_date_format' => '%Y-%m-%d %H:%M:%S',//设置 Server 日志时间格式，格式参考 strftime 的 format
        'open_tcp_keepalive' => true, //检测死连接
        'tcp_keepidle' => 4, //4s没有数据传输就进行检测
        'tcp_keepinterval' => 1, //1s探测一次
        'tcp_keepcount' => 5, //探测的次数，超过5次后还没回包close此连接
    ],
    'route' => [
        'get' => [\easydowork\crontab\JobController::class,'get'], //获取一个任务
        'all' => [\easydowork\crontab\JobController::class,'all'],//获取所有任务
        'set' => [\easydowork\crontab\JobController::class,'set'],//添加一个任务
        'del' => [\easydowork\crontab\JobController::class,'del'], //删除一个任务
        'count' => [\easydowork\crontab\JobController::class,'count'], //任务总数
    ],
];