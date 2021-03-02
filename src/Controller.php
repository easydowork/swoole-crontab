<?php
namespace easydowork\crontab;

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
    }

    abstract public function get():array;

    abstract public function set():array;

    abstract public function all():array;

    abstract public function del():array;

    abstract public function count():array;

}