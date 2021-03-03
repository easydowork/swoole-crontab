<?php
namespace easydowork\crontab\server;

use easydowork\crontab\controller\Controller;
use easydowork\crontab\job\JobProcess;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

/**
 * Class Http
 * @package easydowork
 * @property-read Server $_server
 * @property-read array $_config
 */
class Http
{
    /**
     * @var Server
     */
    protected $_server;

    /**
     * @var array
     */
    protected $_config;

    public function __construct($config=[])
    {

        $defaultConfig = require dirname(__DIR__).'/Config.php';

        $this->_config = array_merge($defaultConfig,$config);

        $this->_server = new Server($this->_config['host'], $this->_config['port'],SWOOLE_PROCESS,$this->_config['sock_type']);

        $this->_server->set($this->_config['set']);

        $this->_server->addProcess((new JobProcess($this->_server,$this->_config))->getProcess());

        $this->_server->on('request', [$this, 'onRequest']);

    }

    /**
     * onRequest
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
        $response->header('Content-Type', 'application/json; charset=utf-8');

        if($request->server['request_method'] != 'POST'){
            $response->status(405);
            $response->end(json_encode([
                'code' => 405,
                'message' => 'method not allow!'
            ]));
        }else{
            $url = trim($request->server['request_uri'] ?? '/', '/');

            if(empty($this->_config['route'][$url])){
                $response->status(404);
                $response->end(json_encode([
                    'code' => 404,
                    'message' => 'action not exist!'
                ]));
            }else{
                try {
                    [$controller,$action] = $this->_config['route'][$url];
                    $class = (new $controller($request, $response));
                    if(!($class instanceof Controller)){
                        throw new \Exception(500,$class.' must be extends '.Controller::class);
                    }
                    $data = $class->{$action}();
                    $response->status(200);
                    $response->end(json_encode($data));
                }catch (\Throwable $e){
                    $response->status(500);
                    $response->end(json_encode([
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ]));
                }
            }
        }
    }

    /**
     * getServer
     * @return Server
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * getConfig
     * @return array
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * on
     * @param $event_name
     * @param callable $callback
     */
    public function on($event_name, callable $callback)
    {
        $this->_server->on($event_name, $callback);
    }

    /**
     * start
     */
    public function start()
    {
        $this->_server->start();
    }

}