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
 * @property string $_jobDataFile
 */
class JobProcess
{

    /**
     * @var Server
     */
    protected $_server;

    /**
     * @var string
     */
    protected $_jobDataFile;

    /**
     * JobProcess constructor.
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->_server = $server;
        $this->_jobDataFile = Config::getInstance()->jobConfig['data_file'];
        $this->initTable();
    }

    /**
     * initTable
     */
    protected function initTable()
    {
        try {
            touch($this->_jobDataFile);

            if(!is_writable($this->_jobDataFile)){
                throw new \Exception('data_file is not writable.');
            }

            $jobTable = JobTable::getInstance(Config::getInstance()->jobConfig['table_size']);
            $data = @unserialize(@file_get_contents($this->_jobDataFile));
            if(!empty($data)){
                foreach ($data as $key => $value){
                    $jobTable->set($key,$value);
                }
            }
            FlagTable::getInstance()->setFlag(true);
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
            $i=1;
            while(true){
                print_r($i++);
                $data = [];
                foreach (JobTable::getInstance()->getTable() as $key =>$value){
                    if($value['status']){
                        JobFacade::getExecute($key,$value);
                    }
                    $data[$key] = $value;
                }
                if (FlagTable::getInstance()->getFlag()){
                    file_put_contents($this->_jobDataFile,serialize($data));
                    FlagTable::getInstance()->setFlag(false);
                }
                Coroutine::sleep(60);
            }
        }, false, 2, true);

        $process->name('SwooleCrontabProcess');

        return $process;
    }

}