<?php

    if(TFW_Core::getUser()->hasRight("Core:users:groups:see")){
        $table = new THtml_Table("users_groups");
        $table
            ->setBordered()
            ->setTitle($this->__("Users groups"))
            ->setModel(TFW_Registry::getModel("Core/User_Group"));

        $table
            ->pushHeader($this->__("ID"), [
                "list_data" => "id",
                "style" => "width:10%",
                "cell_options" => [
                    "style" => "text-align:center;"
                ]
            ])
            ->pushHeader($this->__("Name"), [
                "list_data" => "name"
            ]);

        $table
            ->addFilter(new THtml_Table_Filter_Text("name"))
            ->addFilter(new THtml_Table_Filter_Number("id"));


        // Update and create groups actions
        if(TFW_Core::getUser()->hasRight("Core:users:groups:update"))
            $table->setLineAction('Modal.load(\'group_modal_{{id}}\', \'/users/group/edit/{{id}}\');');

        if(TFW_Core::getUser()->hasRight("Core:users:groups:create")){
            $table->addAction($this->__("Create group"), 'Modal.toggle(\'group_modal\');', "success");
            echo $this->getBlock("Core/Settings_Users_Groups_Edit");
        }

        // Table
        $table
            ->show();
    }