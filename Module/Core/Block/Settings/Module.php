<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */

    $table = new THtml_Table();
    $table
        ->setModel(TFW_Registry::getModel("Core/Module"))
        ->pushHeader($this->__("Key"), [
            "list_data" => "module_key"
        ])
        ->pushHeader($this->__("Status"), [
            "list_data" => "THIS",
            "cell_format" => "Core_THtml_Table_Cell_Module_Status",
            "style"     => "width:25%"
        ])
        ->pushHeader($this->__("Version"), [
            "list_data" => "version",
            "style" => "width:10%;"
        ])
        ->pushHeader("", [
            "list_data" => "THIS",
            "cell_format" => "Core_THtml_Table_Cell_Module_Update",
            "style" => "width:35%;"
        ]);

    $panel = new THtml_Panel();

    $new_update = TFW_Registry::getSetting("core.update");
    $update_well = "";

    if(TFW_Core::getUser()->hasRight("Core.settings.modules") && $new_update){
        $update_btn = new THtml_Input_Button("success", false, "btn-xs");
        $update_btn
            ->setValue($this->__("Update"))
            ->setAction('Core.updateCore();');

        $update_well = "<div class=\"well\">"
            ."<table>"
            ."<tr><td colspan=\"2\">".$this->__("New version available")." : <span style=\"margin-left:15px;font-weight:bold;\">".TFW_Registry::getSetting("core.update.version")."</span></td></tr>"
            ."<tr><td style=\"vertical-align:top\">".$this->__("Description :")."</td><td style=\"margin-left:15px;\">".TFW_Registry::getSetting("core.update.description")."</td></tr>"
            ."<tr><td colspan=\"2\"><br />".$update_btn->get()."</td></tr>"
            ."</table>"
            ."</div>";
    }

    $content = "<div class=\"bs-callout bs-callout-info\">"
        ."<h4>".$this->__("TEFW version : %s", TFW_Core::getTFWVersion())."</h4>"
        .$update_well
        ."</div>";

    $panel->setContent($content);

    $panel
        ->setTitle($this->__("Application's modules"))
        ->setTable($table)
        ->show();