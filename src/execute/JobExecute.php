<?php

namespace easydowork\crontab\execute;

/**
 * Class JobExecute
 * @package easydowork\crontab\execute
 */
abstract class JobExecute
{
    abstract public function run(array $data);
}