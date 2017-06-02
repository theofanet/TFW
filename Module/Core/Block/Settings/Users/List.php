<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/03/2017
     */

    // Create and echo users table then
    if(TFW_Core::getUser()->hasRight("Core:users:users:see")){
        $table = new THtml_Table("users");
        $table
            ->setSorted()
            ->setBordered()
            ->setTitle($this->__("Users list"))
            ->setModel(TFW_Registry::getModel("Core/User"));

        $table
            ->pushHeader($this->__("ID"), [
                "list_data" => "id",
                "cell_options" => [
                    "style" => "text-align:center;"
                ]
            ])
            ->pushHeader($this->__("Group"), [
                "list_data" => "group_name",
                "style"     => "width:15%"
            ])
            ->pushHeader($this->__("First name"), [
                "list_data" => "first_name",
                "style"     => "width:20%"
            ])
            ->pushHeader($this->__("Last name"), [
                "list_data" => "last_name",
                "style"     => "width:20%"
            ])
            ->pushHeader($this->__("Email"), [
                "list_data" => "email",
                "style"     => "width:30%"
            ])
            ->pushHeader($this->__("Active"), [
                "list_data" => "active",
                "style"     => "width:10%",
                "data_mask" => [
                    0 => $this->__("No"),
                    1 => $this->__("Yes")
                ],
                "cell_options" => [
                    "style" => "text-align:center;"
                ]
            ]);

        $table
            ->addFilter(new THtml_Table_Filter_Text("group_name"))
            ->addFilter(new THtml_Table_Filter_Text("first_name"))
            ->addFilter(new THtml_Table_Filter_Text("last_name"))
            ->addFilter(new THtml_Table_Filter_Text("email"))
            ->addFilter(new THtml_Table_Filter_Number("id"))
            ->addFilter(new THtml_Table_Filter_Select("active", [
                0 => $this->__("No"),
                1 => $this->__("Yes")
            ]));

        // Update and create users actions
        if(TFW_Core::getUser()->hasRight("Core:users:users:update"))
            $table->setLineAction('Modal.load(\'user_modal_{{id}}\', \'/users/edit/{{id}}\');');

        if(TFW_Core::getUser()->hasRight("Core:users:users:create")){
            $table->addAction($this->__("Create user"), 'Modal.toggle(\'user_modal\');', "success");
            echo $this->getBlock("Core/Settings_Users_Edit");
        }

        // Echo users table then
        $table->show();
    }

