<?php

namespace easydowork\crontab\base;


/**
 * Class ConfigException
 * @package easydowork\crontab\base
 */
class ConfigException extends \Exception
{
    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return 'Error Configuration';
    }
}
