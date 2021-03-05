<?php
namespace easydowork\crontab\controller;

use Swoole\Http\Response;
use Swoole\Http\Request;

/**
 * Class Controller
 * @package easydowork\crontab
 * @property Request $request
 * @property Response $response
 */
abstract class Controller
{

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * Controller constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->init();
    }

    /**
     * init
     */
    public function init()
    {

    }

    /**
     * success
     * @param array $data
     * @param int $code
     * @param string $message
     * @return array
     */
    public function success($data=[],$code=0,$message='操作成功.')
    {
        return $this->send($code,$message,$data);
    }

    /**
     * error
     * @param string $message
     * @param array $data
     * @param int $code
     * @return array
     */
    public function error($message='操作失败.',$data=[],$code=1)
    {
        return $this->send($code,$message,$data);
    }

    /**
     * send
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array
     */
    public function send(int $code,string $message,array $data)
    {
        $returnData = [
            'code' => $code,
            'message' => $message,
        ];

        if(!empty($data)){
            $returnData['data'] = $data;
        }

        return $returnData;
    }

    abstract public function find():array;

    abstract public function create():array;

    abstract public function all():array;

    abstract public function delete():array;

    abstract public function start():array;

    abstract public function stop():array;

    abstract public function count():array;

}