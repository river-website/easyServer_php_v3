<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:49
 */
namespace easyServer\reactor;

use easyServer\core\singleton;

abstract class reactor extends singleton{
    const eventTime 		= 1;
    const eventRead 		= 2;
    const eventWrite 		= 4;
    const eventSignal	    = 8;
    const eventTimeOnce		= 16;
    const eventClock		= 32;
    const eventExcept 		= 64;

    protected $allEvent = array();
    abstract public function addEvent($fd, $status, $func, $arg = null);
    abstract public function delEvent($fd, $status);
    abstract public function loop();
    static public function factoryMethod(){
        if(extension_loaded('libevent'))return epollReactor::getInstance();
        else return selectReactor::getInstance();
    }
}