<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:49
 */
namespace easyServer\connect;

abstract class connect
{
    protected $socket = null;
    protected $protocol = null;
    protected $remoteAddress = '';
    protected $rBuffer = '';
    protected $wBuffer = '';
    protected $currentPackageSize = 0;
    abstract public function read($socket);
    abstract public function write($socket,$data);
    abstract public function send($data);
    abstract public function close($data);

    static public function factorMethod($connectName){
        if ($connectName == 'tcp')
            return new tcpConnect();
        else
            return;
    }

    /**
     * @return null
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param null $socket
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }

    /**
     * @param string $remoteAddress
     */
    public function setRemoteAddress($remoteAddress)
    {
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @return null
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
    /**
     * @param null $protocol
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    public function getRemoteIp()
    {
        $pos = strrpos($this->remoteAddress, ':');
        return ($pos)?trim(substr($this->remoteAddress, 0, $pos), '[]'):'';
    }
    public function getRemotePort()
    {
        return ($this->remoteAddress)? (int)substr(strrchr($this->remoteAddress, ':'), 1):0;
    }
}