<?php

namespace easydowork\crontab\execute;


use easydowork\crontab\Config;
use Swoole\Coroutine\System;

/**
 * Class BashJobExecute
 * @package easydowork\crontab\execute
 */
class BashJobExecute extends JobExecute
{

    /**
     * run
     * @param array $data
     * @return bool
     */
    public function run(array $data):bool
    {
        $res = System::exec('/bin/bash '.$data['command']);
        return !$res?false:true;
    }

    /**
     * validate
     * @param string $command
     * @return bool
     */
    public static function validate(string $command):bool
    {
        $bashWhitelistFile = Config::getInstance()->jobConfig['bash_whitelist_file'];

        if(empty($bashWhitelistFile)){
            return false;
        }

        $content = file_get_contents($bashWhitelistFile);

        if(empty($bashWhitelist = explode("\n",$content))){
            return  false;
        }

        if(!in_array($command,$bashWhitelist,1)){
            return false;
        }

        return true;
    }
}