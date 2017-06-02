<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */

    $active = "";
    if(isset($log_file))
        $active = $log_file;

    $menu = new THtml_Menu();
    foreach(TFW_Log::getLogFiles() as $file){
        $file = str_replace(".".TFW_Log::EXTENSION, "", $file);
        $menu->addLink($file, "/logs/$file", $active == $file);
    }

    $menu->show();