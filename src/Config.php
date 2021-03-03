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
    ],
    'route' => [
        'get' => [\easydowork\crontab\controller\JobController::class,'get'], //获取一个任务
        'all' => [\easydowork\crontab\controller\JobController::class,'all'],//获取所有任务
        'set' => [\easydowork\crontab\controller\JobController::class,'set'],//添加一个任务
        'del' => [\easydowork\crontab\controller\JobController::class,'del'], //删除一个任务
        'count' => [\easydowork\crontab\controller\JobController::class,'count'], //任务总数
    ],
    'job' => [
        'tableSize' => 1024,
        'dataFile' => dirname(__DIR__).'/data.bin',
    ],
];