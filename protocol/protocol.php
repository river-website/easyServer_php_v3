<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:41
 */
namespace easyServer\protocol;

use easyServer\core\singleton;

abstract class protocol extends singleton
{
    abstract public function decode($data,$connect=null);
    abstract public function encode($data);
    abstract public function getInfo($data);
    static public function factoryMethod($protocolName){
        if ($protocolName == 'http')
            return new httpProtocol();
    }
}