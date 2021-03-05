<?php

namespace easydowork\crontab\execute;


use Swoole\Coroutine\System;

/**
 * Class ShellJobExecute
 * @package easydowork\crontab\execute
 */
class ShellJobExecute extends JobExecute
{

    /**
     * run
     * @param array $data
     * @return bool
     */
    public function run(array $data)
    {
        $res = System::exec($data['command']);
        return !$res?false:true;
    }
}