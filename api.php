<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */

    define('ROOT_PATH', __DIR__);

    require_once 'Core/autoload.php';

    try{
        TAPI_Core::init();
        TAPI_Core::run();
        TAPI_Core::close();
    }
    catch(TFW_Exception $e){
        $e->output();
    }