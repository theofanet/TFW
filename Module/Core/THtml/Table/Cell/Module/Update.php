<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_THtml_Table_Cell_Module_Update extends THtml_Table_Cell{
        /**
         * @param Core_Model_Module $module
         * @param array             $args
         *
         * @return string
         */
        public static function format($module, $args = []){
            $updates = TFW_Registry::getSetting("module.update.".$module->module_key);
            $updatesVersion = TFW_Registry::getSetting("module.update.version.".$module->module_key);

            $html = "";

            if(TFW_Core::getUser()->hasRight("Core.settings.modules") && $updates){
                $button = new THtml_Input_Button("info", false, "btn-xs");
                $upBut  = new THtml_Input_Button("success", false, "btn-xs");
                $upBut
                    ->setValue(self::__("Update"));

                $html .= "<script>$(\"tr\").on('click', '#".$upBut->getId()."', function(){Core.updateModule(\"$module->module_key\");});</script>";

                $html .= $button
                    ->addAttributes([
                        "data-toggle"  => "popover",
                        "data-trigger" => "click",
                        "data-html"    => "true",
                        "title"        => self::__("Version %s", $updatesVersion),
                        "data-content" => TFW_Registry::getSetting("module.update.description.".$module->module_key)
                                         ."<br />".str_replace('"', "'", $upBut->get())
                    ])
                    ->setValue(self::__("New version : %s", $updatesVersion))
                    ->get();
            }

            return $html;
        }
    }