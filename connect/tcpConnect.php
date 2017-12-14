<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:20
 */
namespace easyServer\connect;

use easyServer\connect\tcpState\closed;
use easyServer\connect\tcpState\listening;
use easyServer\connect\tcpState\connecting;
use easyServer\reactor\reactor;

class tcpConnect extends connect
{
    private $state = null;
    static public $maxPackageSize = 10485760;
    private $onMessage = null;
    private $onClosed = null;
    public function read($socket)
    {
        // TODO: Implement read() method.
        $data = $this->state->read($this->socket);
        if($data === false){
            $this->close();
        }else if ($data !== null){
            if(!empty($this->protocol)){
                $this->rBuffer .= $data;
                if($this->currentPackageSize>0){
                    if($this->currentPackageSize > strlen($this->rBuffer))return;
                }else{
                    $protocol = $this->protocol;
                    $this->currentPackageSize = $protocol->getInfo($this->rBuffer);
                    if($this->currentPackageSize == 0)return;
                    else if($this->currentPackageSize>0 && $this->currentPackageSize <= self::$maxPackageSize){
                        if($this->currentPackageSize > strlen($this->rBuffer))return;
                    } else{
                        $this->close();
                        return;
                    }
                }
                $buffer = $this->protocol->decode($this->rBuffer,$this);
            }else
                $buffer = $data;
            if($this->onMessage) {
                try{
                    call_user_func_array($this->onMessage, array($this, $buffer));
                }catch (\Exception $ex){
                }
            }
        }
    }
    public function write($socket, $data)
    {
        // TODO: Implement write() method.
        if(!empty($this->wBuffer)){
            $len = $this->state->write($socket,$this->wBuffer);
            if($len === false){
                $this->close();
            }elseif ($len === true){
                $this->wBuffer = '';
                return true;
            }else{
                $this->wBuffer = $len;
                reactor::factoryMethod()->addEvent($this->socket,reactor::eventWrite,array($this,'onWrite'));
                return true;
            }
        }
    }
    public function send($data)
    {
        // TODO: Implement send() method.
        $data = $this->wBuffer.$data;
        if($this->protocol)$data = $this->protocol->encode($data);
        $len = $this->state->write($this->socket,$data);
        if($len === false){
            $this->close();
        }elseif ($len === true){
            $this->wBuffer = '';
            return true;
        }else{
            $this->wBuffer = $len;
            reactor::factoryMethod()->addEvent($this->socket,reactor::eventWrite,array($this,'onWrite'));
            return true;
        }
    }
    public function close($data=null)
    {
        // TODO: Implement close() method.
        $this->state->close($this->socket);
        $this->setClosed();
        reactor::factoryMethod()->delEvent($this->socket,reactor::eventWrite);
        reactor::factoryMethod()->delEvent($this->socket,reactor::eventRead);
    }

    public function setOnMessage($func){
        $this->onMessage = $func;
    }
    public function setOnClosed($func){
        $this->onClosed = $func;
    }

    public function setListening(){
        $this->state = listening::getInstance();
    }
    public function setConnecting(){
        $this->state = connecting::getInstance();
    }
    public function setClosed(){
        $this->state = closed::getInstance();
    }
}