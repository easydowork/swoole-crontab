<?php
namespace easydowork\crontab\job;

use Swoole\Table;

/**
 * Class JobTable
 * @package easydowork\crontab\job
 * @property-write Table $_table
 */
class JobTable
{
    /**
     * @var Table
     */
    protected $_table;

    /**
     * @var static
     */
    public static $_instance;

    private function __construct($size)
    {
        $this->_table = new Table($size);
        $this->_table->column('name', Table::TYPE_STRING, 64);
        $this->_table->column('run_num', Table::TYPE_FLOAT);
        $this->_table->create();
    }

    /**
     * getTable
     * @return Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * getInstance
     * @param int $size
     * @return static
     */
    public static function getInstance($size=1024)
    {
        if(empty(self::$_instance)){
            self::$_instance = new static($size);
        }

        return self::$_instance;
    }

    /**
     * get
     * @param $key
     * @return bool
     */
    public function get($key)
    {
        return $this->_table->get($key);
    }

    /**
     * set
     * @param $key
     * @param array $data
     * @return bool
     */
    public function set($key,$data=[])
    {
        $res = $this->_table->set($key,$data);
        if($res){
            FlagTable::$_instance->setFlag(true);
        }
        return $res;
    }

    /**
     * del
     * @param $key
     * @return bool
     */
    public function del($key)
    {
        $res = $this->_table->del($key);
        if($res){
            FlagTable::$_instance->setFlag(true);
        }
        return $res;
    }

    /**
     * count
     * @return int
     */
    public function count()
    {
        return $this->_table->count();
    }

}