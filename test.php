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
        //join�������������õ�ǰ���̵߳ȴ����߳�ִ�����
        //ȷ�ϱ�join���߳�ִ�н��������߳�ִ��˳��û��ϵ��
        //Ҳ���ǵ����߳���Ҫ���̵߳Ĵ����������߳���Ҫ�ȴ����߳�ִ�����
        //�õ����̵߳Ľ����Ȼ����������롣
        $thread->join();
    }
}
echo microtime(true)-$a;