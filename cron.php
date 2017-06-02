<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    $_requiredPassword = "WgCu3ozXKV6L2WE";

    if($argc > 1) {
        $password = $argv[1];

        if($password == $_requiredPassword){
            define('ROOT_PATH', __DIR__);

            require_once 'Core/autoload.php';

            try{
                TFW_Core::init(false, true);
                TFW_Core::cron();
                TFW_Core::close();
            }
            catch(TFW_Exception $e){
                $e->output();
            }

        }
        else
            echo "Error".PHP_EOL;
    }