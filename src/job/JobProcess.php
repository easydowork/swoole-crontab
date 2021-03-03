<?php
namespace easydowork\crontab\job;

use Swoole\Process;
use Swoole\Http\Server;
use Swoole\Table;

/**
 * Class JobProcess
 * @package easydowork\crontab\job
 * @property Server $_server
 * @property array $_config
 * @property string $_jobDataFile
 */
class JobProcess
{

    /**
     * @var Server
     */
    protected $_server;

    /**
     * @var array
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_jobDataFile;

    /**
     * JobProcess constructor.
     * @param Server $server
     * @param array $config
     */
    public function __construct(Server $server,$config=[])
    {
        $this->_server = $server;
        $this->_config = $config;
        $this->_jobDataFile = $this->_config['job']['dataFile']??'';
        $this->initTable();
    }

    /**
     * initTable
     */
    protected function initTable()
    {
        try {
            $jobTable = JobTable::getInstance((int)$this->_config['job']['tableSize']??1024);
            $data = @unserialize(@file_get_contents($this->_jobDataFile));
            if(!empty($data)){
                foreach ($data as $key => $value){
                    $jobTable->set($key,$value);
                }
            }
            FlagTable::getInstance()->setFlag(false);
        }catch (\Exception $e){
            //TODO logo
        } finally {
            touch($this->_jobDataFile);
        }
    }

    /**
     * getProcess
     * @return Process
     */
    public function getProcess()
    {
        return new Process(function () {
            while(true){
                if (FlagTable::$_instance->getFlag()){
                    $jobTable = JobTable::getInstance();
                    $data = [];
                    foreach ($jobTable->getTable() as $key =>$value){
                        $data[$key] = $value;
                    }
                    if(is_writable($this->_jobDataFile))
                        file_put_contents($this->_jobDataFile,serialize($data));
                    FlagTable::$_instance->setFlag(false);
                }
            };
        }, false, 2, true);
    }

}