<?php
///**
// * Created by PhpStorm.
// * User: Administrator
// * Date: 2017/12/12
// * Time: 20:51
// */
//include 'autoLoad.php';
//use easyServer\easy;
//
//$server = array(
//    'webServer'=>array(
//        '0.0.0.0:80'=>array(
//            'webSite'=>'/html/'
//        ),
//        '0.0.0.0:88'=>array(
//            'webSite'=>'/html/'
//        ),
//    ),
////    'webSocketServer'=>array(),
//);
//$easy = new easy();
//$easy->start($server);
//


$all = 1000000000;
$num = 20;

class AsyncOperation extends Thread {

    public $start = 0;
    public $end = 0;
    public function __construct($start,$end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function run(){

        for ($i=$this->start;$i<$this->end;$i++){
            $a = $i*9/6+0.6;
        }
    }
}

$ave = $all / $num;
$a= microtime(true);
for ($i=0;$i<$num;$i++){

    $thread= new AsyncOperation($i*$ave,($i+1)*$ave);
    if($thread->start()) {
        //join方法的作用是让当前主线程等待该线程执行完毕
        //确认被join的线程执行结束，和线程执行顺序没关系。
        //也就是当主线程需要子线程的处理结果，主线程需要等待子线程执行完毕
        //拿到子线程的结果，然后处理后续代码。
        $thread->join();
    }
}
echo microtime(true)-$a;