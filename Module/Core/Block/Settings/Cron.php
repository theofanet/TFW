<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */

    $table = new THtml_Table();
    $table
        ->setModel(TFW_Registry::getModel("Core/Cron"))
        ->addMassAction($this->__("Run at next minute"), "/settings/cron/lunch")
        ->pushHeader($this->__("Key"), [
            "list_data" => "code"
        ])
        ->pushHeader($this->__("Status"), [
            "list_data"   => "THIS",
            "cell_format" => "Core_THtml_Table_Cell_Cron_Status",
            "style"       => "width:15%"
        ])
        ->pushHeader($this->__("Last execution"), [
            "list_data" => "last_execution_time",
            "cell_format" => "THtml_Table_Cell_Date",
            "style" => "width: 25%;"
        ])
        ->pushHeader($this->__("Next execution"), [
            "list_data" => "next_execution_time",
            "cell_format" => "THtml_Table_Cell_Date",
            "style" => "width: 25%;"
        ]);

    $panel = new THtml_Panel();

    if(TFW_Core::getUser()->hasRight("Core:settings:cron:restart") && TFW_Registry::getSetting("cron.running") == 1)
        $panel->addAction($this->__("Force CRON re-lunch"), 'Core.performAction(\'/settings/cron/restart\');', "success");

    $panel
        ->setTitle($this->__("Application CRON"))
        ->setTable($table)
        ->show();