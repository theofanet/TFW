<?php

    // TODO : REMOVE MODULES FOR DEPLOYMENT

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */

    define('ROOT_PATH', __DIR__);

    require_once 'Core/autoload.php';

    try{
        TFW_Core::init();
        TFW_Core::run();
        TFW_Core::close();
    }
    catch(TFW_Exception $e){
        $e->output();
    }