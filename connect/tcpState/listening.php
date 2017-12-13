<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:42
 */

namespace easyServer\connect\tcpState;

use easyServer\connect\tcpState\tcpState;

class listening extends tcpState
{
    public function read($socket)
    {
        // TODO: Implement read() method.
        $newSocket = stream_socket_accept($socket, 0, $remoteAddress);
        if(!empty($newSocket)){
            return array($newSocket,$remoteAddress);
        }
    }
    public function write($socket, $data)
    {
        // TODO: Implement write() method.
    }
    public function close($socket)
    {
        // TODO: Implement close() method.
        fclose($socket);
    }
}