<?php
namespace easydowork\crontab;

use easydowork\crontab\base\ConfigException;
use easydowork\crontab\base\Instance;
use easydowork\crontab\controller\JobController;
use easydowork\crontab\execute\ShellJobExecute;
use easydowork\crontab\execute\UrlJobExecute;

/**
 * Class Config
 * @package easydowork\crontab\server
 * @property string $host
 * @property int $port
 * @property int $sock_type
 * @property array $settings
 * @property array $routes
 * @property array $jobConfig
 * @property array $runLogConfig
 */
class Config
{
    use Instance;

    public $host = '0.0.0.0';

    public $port = 9501;

    public $sock_type = SWOOLE_SOCK_TCP;

    public $settings = [
        'max_request' => 1000,
        'daemonize' => false,
        'log_file' => null,
        'log_date_format' => '%Y-%m-%d %H:%M:%S',//设置 Server 日志时间格式，格式参考 strftime 的 format
    ];

    /**
     * @var array
     */
    public $routes = [
        'find' => [JobController::class,'find'], //获取一个任务
        'create' => [JobController::class,'create'],//添加一个任务
        'all' => [JobController::class,'all'],//获取所有任务
        'delete' => [JobController::class,'delete'], //删除一个任务
        'count' => [JobController::class,'count'], //任务总数
        'start' => [JobController::class,'start'], //开始一个任务
        'stop' => [JobController::class,'stop'], //停止一个任务
    ];

    /**
     * @var array
     */
    public $jobConfig = [
        'run_types' => [
            'url' => UrlJobExecute::class,
            'shell' => ShellJobExecute::class,
        ],
        'table_size' => 1024,
        'data_file' => null
    ];

    /**
     * @var array
     */
    public $runLogConfig = [
        'path' => null,
        'console' => false,
    ];

    /**
     * Config constructor.
     * @param array $config
     */
    private function __construct(array $config=[])
    {
        foreach ($config as $key => $value){
            if(isset($this->{$key})){
                if(is_array($this->{$key})){
                    $value = array_merge($this->{$key},$value);
                }
                $this->{$key} = $value;
            }
        }
        $this->checkConfig();
    }

    /**
     * checkConfig
     */
    public function checkConfig()
    {
        try {
            if(empty($this->settings['worker_num'])){
                $this->settings['worker_num'] = swoole_cpu_num()*2;
            }

            if(empty($this->jobConfig['data_file'])){
                throw new ConfigException('data_file must be configuration.');
            }

            if(!is_file($this->jobConfig['data_file'])){
                touch($this->jobConfig['data_file']);
            }else{
                if(!is_writable($this->jobConfig['data_file'])){
                    throw new ConfigException('data_file is not writable.');
                }
            }

            if(empty($this->runLogConfig['path'])){
                $this->runLogConfig['path'] = getcwd();
            }

            if( $this->settings['daemonize']){
                $this->runLogConfig['console'] = false;
            }

        }catch (\Exception $e){
            print_ln($e->getMessage());
            exit();
        }

    }


}