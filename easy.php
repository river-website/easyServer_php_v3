<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:38
 */
namespace easyServer;
use easyServer\server\webServer;


class easy{
    private function back(){
        return;
        $pid  = pcntl_fork();
        if($pid > 0)exit();
    }

    public function start($serverData){
        $this->back();
        array_map(function ($server,$data){
            $ser = new webServer();
            $ser->start($data);
        },array_keys($serverData),array_values($serverData));
        $this->monitorServer();
    }
    private function monitorServer(){

    }
}