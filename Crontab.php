<?php
require 'vendor/autoload.php';

use easydowork\crontab\Http;

\Swoole\Runtime::enableCoroutine(SWOOLE_HOOK_ALL);

$http = new Http();

$http->start();