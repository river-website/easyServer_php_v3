<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:40
 */
namespace easyServer\server;

use easyServer\connect\connect;
use easyServer\reactor\reactor;

class baseServer extends server{
    private $fdMap = array();
    public function start($data = null)
    {
        // TODO: Implement start() method.
        reactor::factoryMethod()->loop();
    }
    public function init($data = null,$server = null)
    {
        // TODO: Implement init() method.
        // tcp://0.0.0.0:80
        return array_reduce($data,function($ret,$addr) use ($server){
            $connectName = explode( ":",$addr)[0];
            $addr = explode("//",$addr)[1];
            $connect = connect::factorMethod($connectName);
            $socket = $this->createSocket($addr);
            $connect->setSocket($socket);
            $connect->setOnMessage(array($this,'onMessage'));
            $connect->setListening();
            $this->addEvent($socket,reactor::eventRead,array($connect,"read"));
            $ret[$addr] = (int)$socket;
            $this->fdMap[(int)$socket] = $server;
            return $ret;
        });
    }
    public function addEvent($fd, $status, $func, $arg = null){
        reactor::factoryMethod()->addEvent($fd,$status,$func,$arg);
    }
    private function createSocket($addr){
        $socket = stream_socket_server($addr);
        if (!$socket) exit();
        stream_set_blocking($socket, 0);
        return $socket;
    }

    public function onMessage($connect, $data)
    {
        // TODO: Implement onMessage() method.
        $new_socket = $data[0];
        $remoteAddress = $data[1];

        stream_set_blocking($new_socket,0);

        $server = $this->fdMap[(int)$connect->getSocket()];

        $tcp = connect::factorMethod("tcp");
        $tcp->setSocket($new_socket);
        $tcp->setRemoteAddress($remoteAddress);
        $tcp->setOnMessage(array($server,'onMessage'));
        $tcp->setOnClosed(array($server,'onClose'));
        $tcp->setProtocol($server->protocol);
        $tcp->setConnecting();
        $this->addEvent($new_socket, reactor::eventRead, array($tcp, 'read'));
        if(!empty($this->onConnect))
            call_user_func($this->onConnect,$tcp);
    }

}