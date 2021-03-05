<?php

namespace easydowork\crontab\execute;

/**
 * Class UrlJobExecute
 * @package easydowork\crontab\execute
 */
class UrlJobExecute extends JobExecute
{

    /**
     * run
     * @param array $data
     * @return bool
     */
    public function run(array $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $data['command']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); //返回状态码
        curl_close($ch);

       return $httpCode==200;
    }
}