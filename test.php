<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:51
 */
include 'autoLoad.php';
use easyServer\easy;

$server = array(
    'webServer'=>array(
        '0.0.0.0:80'=>array(
            'webSite'=>'/html/'
        )
    ),
//    'webSocketServer'=>array(),
);
$easy = new easy();
$easy->start($server);


