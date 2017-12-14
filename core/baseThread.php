<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/14
 * Time: 13:49
 */
namespace easyServer\core;

class baseThread extends \Thread{
    private $func = null;
    private $args = null;
    public function __construct($func,$args=null)
    {
        $this->func = $func;
        $this->args = $args;
    }
    public function run()
    {
        if(!empty($this->func)){
            try{
                call_user_func_array($this->func,$this->args);
            }catch (\Exception $e){

            }
        }
    }
}