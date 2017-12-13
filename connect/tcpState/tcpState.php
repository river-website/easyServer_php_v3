<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:42
 */
namespace easyServer\connect\tcpState;

use easyServer\core\singleton;

abstract class tcpState extends singleton
{
    abstract public function read($socket);
    abstract public function write($socket,$data);
    abstract public function close($socket);
}