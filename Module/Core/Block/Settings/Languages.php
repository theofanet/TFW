<?php

    $table = new THtml_Table();

    $table
        ->setModel(TFW_Registry::getModel("Core/Language"))
        ->pushHeader($this->__("Code"), [
            "list_data" => "lang_code",
            "style" => "width:20%;"
        ])
        ->pushHeader($this->__("Name"), [
            "list_data" => "lang_name"
        ])
        ->addFilter(new THtml_Table_Filter_Text("lang_code"))
        ->addFilter(new THtml_Table_Filter_Text("lang_name"));

    if(TFW_Core::getUser()->hasRight("Core:settings:update")){
        echo $this->getBlock("Core/Settings_Languages_Create");
        $table->addAction($this->__("Create language"), 'Modal.toggle(\'new_language_modal\');', "success");
    }

    $panel = new THtml_Panel();
    $panel
        ->setTitle($this->__("Application languages"))
        ->setTable($table)
        ->show();