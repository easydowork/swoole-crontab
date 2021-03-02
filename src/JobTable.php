<?php
namespace easydowork\crontab;

use Swoole\Table;

abstract class JobTable
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

}