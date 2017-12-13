<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:41
 */
namespace easyServer\server;
use easyServer\protocol\protocol;
class webServer extends server
{
    private $connectToWebPath = array();
    public $protocol = null;
    public function start($data = null)
    {
        // TODO: Implement start() method.
    }

    public function init($data = null,$server = null)
    {
        // TODO: Implement init() method.
        $this->protocol = protocol::factoryMethod("http");
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
    public function onMessage($connect, $data)
    {
        // TODO: Implement onMessage() method.
        $connect->send("xxxx");
    }
}