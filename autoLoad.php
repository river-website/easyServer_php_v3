<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 20:32
 */

namespace easyServer;

class autoLoad{
    /* 路径映射 */
    public static $vendorMap = array(
        'easyServer' => __DIR__ . DIRECTORY_SEPARATOR ,
    );
    public static $curDir = __DIR__ . DIRECTORY_SEPARATOR;
    public static $allFile = array();
    /**
     * 自动加载器
     */
    public static function load($class)
    {
        echo $class."\n";
        $file = self::findFile($class);
        if (file_exists($file)) {
            self::includeFile($file);
        }
    }

    public static function auto($class){
        $fileName = substr($class, strpos($class, '\\')).".php"; // 顶级命名空间
        if(!empty(self::$allFile[$fileName])){
            $file = self::$allFile[$fileName];
            if (file_exists($file))
                include $file;
        }
    }
    public static function read_all_dir ( $dir )
    {
        $result = array();
        $handle = opendir($dir);
        if ( $handle ) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $cur_path = $dir . $file;
                    if (is_dir($cur_path))
                        $result = array_merge($result, self::read_all_dir($cur_path));
                    else if(strpos($file,".php") > 1)
                        $result[$file] = $cur_path;
                }
            }
            closedir($handle);
        }
        return $result;
    }

    /**
     * 解析文件路径
     */
    private static function findFile($class)
    {
        $vendor = substr($class, 0, strpos($class, '\\')); // 顶级命名空间
        $vendorDir = self::$vendorMap[$vendor]; // 文件基目录
        $filePath = substr($class, strlen($vendor)) . '.php'; // 文件相对路径
        return strtr($vendorDir . $filePath, '\\', DIRECTORY_SEPARATOR); // 文件标准路径
    }

    /**
     * 引入文件
     */
    private static function includeFile($file)
    {
        if (is_file($file)) {
            include $file;
        }
    }
}
spl_autoload_register('easyServer\autoLoad::load');
//Load::$allFile = Load::read_all_dir(Load::$curDir);