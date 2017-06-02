<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_THtml_Table_Cell_Module_Status extends THtml_Table_Cell{

        public static function format($module, $args = []){
            $status = TFW_Registry::getRegistered("module:$module->module_key");

            $class   = "success";
            $label   = "Enabled";

            if(!$status || !$status->isActive()){
                $class   = "danger";
                $label   = "Disabled";
            }

            $label = self::__($label);

            return "<span class=\"label label-$class\">$label</span>";
        }
    }