<?php
namespace easydowork\crontab\job;

use easydowork\crontab\base\Instance;
use Swoole\Table;

/**
 * Class JobTable
 * @package easydowork\crontab\job
 * @property-write Table $_table
 */
class FlagTable
{
    use Instance;

    /**
     * @var Table
     */
    private $_table;

    /**
     * FlagTable constructor.
     */
    private function __construct()
    {
        $this->_table = new Table(1);
        $this->_table->column('flag', Table::TYPE_INT, 1);
        $this->_table->create();
    }

    /**
     * getFlag
     * @return bool
     */
    public function getFlag()
    {
        return (bool)($this->_table->get('flag')['flag']??0);
    }

    /**
     * setFlag
     * @param bool $flag
     * @return bool
     */
    public function setFlag(bool $flag)
    {
        return $this->_table->set('flag',['flag'=>(int)$flag]);
    }
}