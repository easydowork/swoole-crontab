<?php

namespace easydowork\crontab\execute;

/**
 * Class JobExecute
 * @package easydowork\crontab\execute
 */
abstract class JobExecute
{
    abstract public function run(array $data):bool;

    /**
     * validate
     * @param string $command
     * @return bool
     */
    public static function validate(string $command):bool
    {
        return true;
    }
}