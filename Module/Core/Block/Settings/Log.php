<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */

    $panel = new THtml_Panel();
    $panel
        ->setTitle($this->__("Log file content"));

    if(isset($log_file)){
        $data = TFW_Log::getData($log_file);

        $table = new THtml_Table();
        $table
            ->pushHeader($this->__("User"), ["style" => "width:15%"])
            ->pushHeader($this->__("Date"), ["style" => "width:15%"])
            ->pushHeader($this->__("Trace"), ["style" => "width:25%"])
            ->pushHeader($this->__("Data"));

        foreach($data as list($user, $timestamp, $trace, $content)){
            $table
                ->addCell($user)
                ->addCell(TFW_Registry::getHelper("Core/Time")->formatDate($timestamp))
                ->addCell($trace)
                ->addCell($content)
                ->addCurrentLine();
        }

        if(TFW_Core::getUser()->hasRight("Core:logs:download"))
            $panel->addAction($this->__("Download"), 'Core.openUrl(\'/logs/'.$log_file.'/download\', true);', "success");
        if(TFW_Core::getUser()->hasRight("Core:logs:delete"))
            $panel->addAction($this->__("Delete"), 'Core.openUrl(\'/logs/'.$log_file.'/delete\');', "danger");

        $panel
            ->setTitle($this->__("Log file content : %s", $log_file))
            ->setTable($table);
    }

    $panel->show();