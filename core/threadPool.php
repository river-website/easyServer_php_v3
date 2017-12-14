<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/14
 * Time: 10:41
 */
namespace easyServer\core;

class threadPool{
    private $threads = [];
    private $events = null;
    private $maxSize = 0;
    public function __construct($maxSize)
    {
        $this->maxSize = $maxSize;
        $this->events = new baseQueue(true);
    }
    private function createThread(){
        $thread = new baseThread(array($this,'runThread'));
        array_push($this->threads,$thread);
        $thread->start();
    }
    public function addEvent($func,$args=null){
        $this->events->push(array($func,$args));
        if(count($this->threads)<$this->maxSize)
            $this->createThread();
    }
    public function runThread(){
        while (true){
            $event = $this->events->pop();
            if(count($event) != 2)continue;
            $func = $event[0];
            $args = $event[1];
            try{
                call_user_func_array($func,$args);
            }catch (\Exception $e){

            }
        }
    }
}