<?php
require 'vendor/autoload.php';

use Swoole\Runtime;
use easydowork\crontab\server\Http;
Runtime::enableCoroutine(SWOOLE_HOOK_ALL);

swoole_set_process_name('SwooleCrontab');

$http = new Http();

$server = $http->getServer();

$http->start();