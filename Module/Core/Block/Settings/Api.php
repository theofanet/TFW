<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */

    $table = new THtml_Table();
    $table
        ->setModel(TFW_Registry::getModel("Core/Api_Key"))
        ->pushHeader("", [
            "list_data"   => "THIS",
            "cell_format" => "Core_THtml_Table_Cell_Active",
            "style"       => "width:10%"
        ])
        ->pushHeader($this->__("Label"), [
            "list_data" => "label"
        ])
        ->pushHeader($this->__("Key"), [
            "list_data" => "value",
            "style"     => "width:40%"
        ])
        ->pushHeader($this->__("Rights group"), [
            "list_data"   => "group_name",
            "style"       => "width: 25%;"
        ]);



    // Update and create api keys actions
    if(TFW_Core::getUser()->hasRight("Core:settings:api:update"))
        $table->setLineAction('Modal.load(\'edit_api_key_{{id}}\', \'/settings/api/edit/{{id}}\');');

    if(TFW_Core::getUser()->hasRight("Core:settings:api:create")){
        $table->addAction($this->__("Create new API key"), 'Modal.toggle(\'edit_api_key\');', "success");
        echo $this->getBlock("Core/Settings_Api_Edit");
    }

    $panel = new THtml_Panel();
    $panel
        ->setTitle($this->__("Application API keys"))
        ->setTable($table)
        ->show();