<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:08
 */

namespace easyServer\reactor;


class epollReactor extends reactor
{
    private $base 			= null;

    public function __construct(){
        $this->base = event_base_new();
    }

    public function addEvent($fd, $status, $func, $arg = null)
    {
        // TODO: Implement addEvent() method.
        switch ($status){
            case reactor::eventTimeOnce:
            case reactor::eventTime:
            case reactor::eventClock:{
                if(reactor::eventClock == $status) {
                    // $fd 如 03:15:30,即每天3:15:30执行
                    $time = strtotime($fd);
                    $now = time();
                    if ($now >= $time)$time = strtotime('+1 day', $time);
                    $time = ($time - $now) * 1000;
                }
                else $time = $fd * 1000;

                $event = event_new();
                if (!event_set($event, 0, EV_TIMEOUT,array($this,'onTime'), array($event,$fd,$status,$func,$arg))) return false;
                if (!event_base_set($event, $this->base)) return false;
                if (!event_add($event, $time)) return false;
                $this->allEvent[(int)$event][$status] = $event;
                return (int)$event;
            }
                break;
            case reactor::eventSignal: {
                $event = event_new();
                if (!event_set($event, $fd, $status | EV_PERSIST, $func, array($arg))) return false;
                if (!event_base_set($event, $this->base)) return false;
                if (!event_add($event)) return false;
                $this->allEvent[(int)$fd][$status] = $event;
            }
                break;
            case reactor::eventRead:
            case reactor::eventWrite: {
                $event = event_new();
                if (!event_set($event, $fd, $status | EV_PERSIST, $func, array($arg))) return false;
                if (!event_base_set($event, $this->base)) return false;
                if (!event_add($event)) return false;
                $this->allEvent[(int)$fd.$status] = $event;
            }
                break;
            default:
                break;
        }
        return true;

    }
    public function delEvent($fd, $status)
    {
        // TODO: Implement delEvent() method.
        $key = (int)$fd.$status;
        if(!empty($this->allEvent[$key])){
            $ev = $this->allEvent[$key];
            event_del($ev);
            unset($this->allEvent[$key]);
        }
    }
    public function loop()
    {
        // TODO: Implement loop() method.
        event_base_loop($this->base);

    }

    public function onTime($fd,$type,$args){
        if(count($args) != 5)return;
        $event  = $args[0];
        $fd     = $args[1];
        $status = $args[2];
        $func   = $args[3];
        $arg    = $args[4];
        if($status != ezReactor::eventTimeOnce) {
            if ($status == ezReactor::eventClock) {
                // $fd 如 03:15:30,即每天3:15:30执行
                $time = strtotime($fd);
                $now = time();
                $time = strtotime('+1 day', $time);
                $time = ($time - $now) * 1000;
            }
            else $time = $fd * 1000;
            event_add($event,$time);
        }
        try{
            call_user_func($func,$arg);
        }catch (Exception $ex){
            ezLog($ex->getMessage());
        }
    }
}