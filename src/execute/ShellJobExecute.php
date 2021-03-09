<?php

namespace easydowork\crontab\execute;


use easydowork\crontab\Config;
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
        $shellWhitelistFile = Config::getInstance()->jobConfig['shell_whitelist_file'];

        if(empty($shellWhitelistFile)){
            return false;
        }

        $content = file_get_contents($shellWhitelistFile);

        if(empty($shellWhitelist = explode("\n",$content))){
            return  false;
        }

        if(!in_array($command,$shellWhitelist,1)){
            return false;
        }

        return true;
    }
}