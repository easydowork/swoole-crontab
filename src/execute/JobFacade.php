<?php

namespace easydowork\crontab\execute;

use easydowork\crontab\job\JobTable;
use easydowork\crontab\Config;
use easydowork\crontab\Logger;
use Swoole\Timer;

/**
 * Class JobExecute
 * @package easydowork\crontab\execute
 */
class JobFacade
{

    /**
     * getExecute
     * @param $key
     * @param $data
     */
    public static function getExecute($key,&$data)
    {

        if($data['start_time'] && $data['start_time'] < time()){
            return;
        }

        if($data['stop_time'] && $data['stop_time'] < time()){
            return;
        }

        $executeClass = Config::getInstance()->jobConfig['run_types'][$data['run_type']]??'';

        if(empty($executeClass))
            return;

        $times = FormatParser::getInstance()->parse($data['format']);

        if(empty($times))
            return;

        $now = time();

        foreach ($times as $time) {
            $t = $time-$now;
            if ($t <= 0) {
                $t = 0.001;
            }

            $timeId = Timer::after($t*1000,function () use($executeClass,$data){
                try{
                    /** @var $execute JobExecute */
                    $execute = new $executeClass();
                    if($execute->run($data) === false){
                        Logger::getInstance()->error('执行定时任务['.$data['name'].'],返回结果失败.');
                    }else{
                        Logger::getInstance()->info('执行定时任务['.$data['name'].'],返回结果成功.');
                    }
                }catch (\Exception $e){
                    Logger::getInstance()->error('执行定时任务异常['.$data['name'].']'.$e->getMessage());
                }
            });
        }
    }
}