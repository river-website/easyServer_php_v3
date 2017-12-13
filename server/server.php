<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 22:39
 */
namespace easyServer\server;

use easyServer\core\singleton;

abstract class server extends singleton{
    abstract public function start($data=null);
    abstract public function init($data=null,$server = null);
    abstract public function onMessage($connect,$data);
    static public function factoryMethod($serverName = null){
        if($serverName == 'webServer')return webServer::getInstance();
        else return baseServer::getInstance();
    }
}