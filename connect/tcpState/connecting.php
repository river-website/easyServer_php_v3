<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:41
 */
namespace easyServer\connect\tcpState;

use easyServer\connect\tcpState\tcpState;

class connecting extends tcpState
{
    public function read($socket)
    {
        // TODO: Implement read() method.
        $buffer = fread($socket, 65535);
        // Check connection closed.
        if ($buffer === '' || $buffer === false || !is_resource($socket))
            return false;
        return $buffer;
    }
    public function write($socket, $data)
    {
        // TODO: Implement write() method.
        $len = fwrite($socket,$data,8192);
        if($len <= 0){
            if (!is_resource($socket) || feof($socket)) return false;
            else return $data;
        }else if($len != strlen($data))
            return substr($data, $len);
        return true;
    }
    public function close($socket)
    {
        // TODO: Implement close() method.
        fclose($socket);
    }
}