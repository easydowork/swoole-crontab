<?php
namespace easydowork\crontab\job;

use easydowork\crontab\execute\JobFacade;
use easydowork\crontab\Config;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Http\Server;

/**
 * Class JobProcess
 * @package easydowork\crontab\job
 * @property Server $_server
 */
class JobProcess
{

    /**
     * @var Server
     */
    protected $_server;

    /**
     * JobProcess constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->_server = $server;
        $this->initTable();
    }

    /**
     * initTable
     */
    protected function initTable()
    {
        $jobDataFile = Config::getInstance()->jobConfig['data_file'];
        try {
            touch($jobDataFile);

            if(!is_writable($jobDataFile)){
                throw new \Exception('data_file is not writable.');
            }

            $jobTable = JobTable::getInstance(Config::getInstance()->jobConfig['table_size']);
            $data = @unserialize(@file_get_contents($jobDataFile));
            if(!empty($data)){
                foreach ($data as $key => $value){
                    $jobTable->set($key,$value);
                }
            }
        }catch (\Exception $e){
            print_ln($e->getMessage());
            exit();
        }
    }

    /**
     * getProcess
     * @return Process
     */
    public function getProcess()
    {
        $process = new Process(function () {
            while(true){
                JobTable::getInstance()->each(function ($key,&$value){
                    JobFacade::getExecute($key,$value);
                });
                Coroutine::sleep(60);
            }
        }, false, 2, true);

        $process->name('SwooleCrontabProcess');

        return $process;
    }

}