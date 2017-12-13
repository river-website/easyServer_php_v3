<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 14:57
 */
 namespace easyServer\reactor;

class selectReactor extends reactor
{
    private $rEvents = array();
    private $wEvents = array();
    private $eEvents = array();
    public function addEvent($fd, $status, $func, $arg = null)
    {
        // TODO: Implement addEvent() method.
        $fdKey = (int)$fd;
        switch ($status) {
            case reactor::eventRead:
                $this->allEvent[$fdKey][$status] = array($func,$arg);
                $this->rEvents[$fdKey] = $fd;
                break;
            case reactor::eventWrite:
                $this->allEvent[$fdKey][$status] = array($func,$arg);
                $this->wEvents[$fdKey] = $fd;
                break;
            case reactor::eventExcept:
                $this->allEvent[$fdKey][$status] = array($func,$arg);
                $this->eEvents[$fdKey] = $fd;
                break;
            default:
                break;
        }
    }
    public function delEvent($fd, $status)
    {
        // TODO: Implement delEvent() method.
        $fd_key = (int)$fd;
        if(!empty($this->allEvent[$fd_key][$status]))
            unset($this->allEvent[$fd_key][$status]);
        switch ($status) {
            case reactor::eventRead:
                if(!empty($this->rEvents[$fd_key]))
                    unset($this->rEvents[$fd_key]);
                break;
            case reactor::eventWrite:
                if(!empty($this->wEvents[$fd_key]))
                    unset($this->wEvents[$fd_key]);
                break;
            case reactor::eventExcept:
                if(!empty($this->eEvents[$fd_key]))
                    unset($this->eEvents[$fd_key]);
                break;
            default:
                break;
        }
    }
    public function loop()
    {
        // TODO: Implement loop() method.
        while(true){
            $read = $this->rEvents;
            $write = $this->wEvents;
            $except = $this->eEvents;
            $ret = stream_select($read, $write, $except, 10);
            if(!$ret) continue;
            foreach ($read as $fd) {
                $fd_key = (int)$fd;
                $ev = $this->allEvent[$fd_key][reactor::eventRead];
                call_user_func_array($ev[0],array($fd,$ev[1]));
            }
            foreach ($write as $fd) {
                $fd_key = (int)$fd;
                $ev = $this->allEvent[$fd_key][reactor::eventWrite];
                call_user_func_array($ev[0],array($fd,$ev[1]));
            }
            foreach ($except as $fd) {
                $fd_key = (int)$fd;
                $ev = $this->allEvent[$fd_key][reactor::eventExcept];
                call_user_func_array($ev[0],array($fd,$ev[1]));
            }
        }
    }
}