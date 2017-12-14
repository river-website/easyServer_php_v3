<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:41
 */
namespace easyServer\server;
use easyServer\protocol\protocol;
use easyServer\core\threadPool;
class webServer extends server
{
    private $connectToWebPath = array();
    public $protocol = null;
    private $threadPool = null;
    public function start($data = null)
    {
        // TODO: Implement start() method.
    }

    public function init($data = null,$server = null)
    {
        // TODO: Implement init() method.
        $this->protocol = protocol::factoryMethod("http");
        $this->threadPool = new threadPool(100);
        $addrs = array_map(function($addr){
            return "tcp://$addr";
        },array_keys($data));
        $server =server::factoryMethod();
        $addrFds = $server->init($addrs,$this);
        array_map(function($addr,$webPaths)use($addrFds){
            $fd = $addrFds[$addr];
            $this->connectToWebPath[$fd] = $webPaths;
        },array_keys($data),array_values($data));
    }
    public function doRequest($connect, $data){
        var_dump($connect);
        var_dump($data);
        $connect->send("xxx");
    }
    public function onMessage($connect, $data)
    {
        // TODO: Implement onMessage() method.
//        $this->threadPool->addEvent(array($this,"doRequest"),array($connect,$data));
    }
}