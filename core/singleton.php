<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 10:34
 */
namespace easyServer\core;

class singleton{
    static $models = [];
    static public function getInstance(){
        $name =  get_called_class();
        if( !isset( self::$models[$name] ) ){
            self::$models[$name] = new $name();
        }
        return self::$models[$name];
    }
}