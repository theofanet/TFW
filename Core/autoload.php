<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */

    error_reporting(E_ERROR);
    ini_set('display_errors', '0');

    if(!defined('ROOT_PATH'))
        define("ROOT_PATH", "");

    if(file_exists(ROOT_PATH . '/vendor/autoload.php'))
        require ROOT_PATH . '/vendor/autoload.php';

    class Autoload{

        protected static $_instance;

        public function __construct(){

        }

        static public function getInstance(){
            if (!self::$_instance)
                self::$_instance = new Autoload();

            return self::$_instance;
        }

        public static function register(){
            spl_autoload_register(array(self::getInstance(), 'autoload'));
        }

        public function autoload($class_name){
            $parts = explode('_', $class_name);
            $class = array_pop($parts).'.php';

            $core_path = ROOT_PATH.DIRECTORY_SEPARATOR
                .'Core'.DIRECTORY_SEPARATOR;

            $module_path = ROOT_PATH.DIRECTORY_SEPARATOR
                .'Module'.DIRECTORY_SEPARATOR;

            $parts_dir = ltrim(implode(DIRECTORY_SEPARATOR, $parts), DIRECTORY_SEPARATOR);

            $path   = $parts_dir.DIRECTORY_SEPARATOR.$class;

            $module = $module_path.$path;
            $core   = $core_path.$path;

            if(is_file($module) === true)
                require_once $module;
            else if(is_file($core) === true)
                require_once $core;
        }

    }

    Autoload::register();