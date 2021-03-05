<?php
namespace easydowork\crontab\job;

use easydowork\crontab\base\Instance;
use easydowork\crontab\Config;
use easydowork\crontab\execute\FormatParser;
use Swoole\Table;

/**
 * Class JobTable
 * @package easydowork\crontab\job
 * @property-write Table $_table
 */
class JobTable
{
    use Instance;

    /**
     * @var Table
     */
    private $_table;

    /**
     * JobTable constructor.
     * @param $size
     */
    private function __construct($size)
    {
        $this->_table = new Table($size);
        $this->_table->column('name', Table::TYPE_STRING, 20);  //名称
        $this->_table->column('start_time', Table::TYPE_INT,8);  //开始执行时间
        $this->_table->column('stop_time', Table::TYPE_INT,8);  //停止执行时间
        $this->_table->column('format', Table::TYPE_STRING,30); //crontab格式 加秒
        $this->_table->column('run_type', Table::TYPE_STRING,10);  //运行类型
        $this->_table->column('command', Table::TYPE_STRING,$size);  //执行任务 判断是url还是shell名称
        $this->_table->column('status', Table::TYPE_INT,1);  //状态 1正常 0关闭
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
            FlagTable::getInstance()->setFlag(true);
        }
        return $res;
    }

    /**
     * start
     * @param $key
     * @param $data
     * @return bool
     */
    public function start($key,$data)
    {
        $data['status'] = 1;
        return $this->set($key,$data);
    }

    /**
     * stop
     * @param $key
     * @param $data
     * @return bool
     */
    public function stop($key,$data)
    {
        $data['status'] = 0;
        return $this->set($key,$data);
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
            FlagTable::getInstance()->setFlag(true);
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

    /**
     * checkData
     * @param $data
     * @return bool|string
     */
    public function checkData($data)
    {
        if(empty($data['name']))
            return '任务名称不能为空.';

        if(strlen($data['name']) > 20)
            return '任务名称不能大于20个字符.';

        if(!empty($data['start_time'])){
            if(strtotime(date('m-d-Y H:i:s',$data['start_time'])) !== $data['start_time']) {
                return '开始时间必须是时间戳或不设置.';
            }
        }

        if(!empty($data['stop_time'])){
            if(strtotime(date('m-d-Y H:i:s',$data['stop_time'])) !== $data['start_time']) {
                return '结束时间必须是时间戳或不设置.';
            }
        }

        if(empty($data['command']))
            return '任务命令不能为空.';

        if(strlen($data['command']) > $this->_table->size)
            return '任务命令不能大于'.$this->_table->size.'个字符.';

        if(empty($data['run_type']))
            return '任务类型不能为空.';

        $runTypes = Config::getInstance()->jobConfig['run_types'];

        if(empty($runTypes[$data['run_type']]))
            return '任务类型错误,必须为:'.implode(',',$runTypes).'其中之一.';

        try {
            FormatParser::getInstance()->parse($data['format']??'');
        }catch (\Exception $e){
            return 'crontab格式错误.';
        }

        return true;
    }

}