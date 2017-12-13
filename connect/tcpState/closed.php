<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:41
 */
namespace easyServer\connect\tcpState;

use easyServer\connect\tcpState\tcpState;

class closed extends tcpState
{
    public function read($socket)
    {
        // TODO: Implement read() method.
    }
    public function write($socket, $data)
    {
        // TODO: Implement write() method.
    }
    public function close($socket)
    {
        // TODO: Implement close() method.
    }
}