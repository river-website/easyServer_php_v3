<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/14
 * Time: 11:47
 */
namespace easyServer\core;

class baseMutex{
    private $mutex = null;
    public function __construct($lock = false){
        $this->mutex = \Mutex::create($lock);
    }
    public function lock(){
        \Mutex::lock($this->mutex);
    }
    public function unLock(){
        \Mutex::unLock($this->mutex);
    }
    public function __destruct(){
        \Mutex::destroy($this->mutex);
    }
}