<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 02/03/2017
     */
    class Core_Cron_Updates extends TFW_Cron{


        public function checkUpdates(){
            // First check core update
            $url = TFW_Core::getUpdaterUrl();
            
            if($url){
                $rest = new TFW_Rest($url);

                $rest
                    ->addHeader("Authorization", "")
                    ->addHeader("Api-Key", "")
                    ->addHeader("Content-Type", "application/json");

                $coreVersion = $rest->get("/info/core/version");
                if($coreVersion){
                    if(version_compare(TFW_Core::getTFWVersion(), $coreVersion["version"])){
                        TFW_Registry::setSetting("core.update", $coreVersion["id"]);
                        TFW_Registry::setSetting("core.update.version", $coreVersion["version"]);
                        TFW_Registry::setSetting("core.update.description", $coreVersion["description"]);
                    }
                }

                $modules = TFW_Registry::getRegistered("module");
                /**
                 * @var TFW_Module $module
                 */
                foreach($modules as $module){
                    $module_version = $rest->post("/info/module/version", [$module->getKey()]);

                    if($module_version && isset($module_version[$module->getKey()])){
                        if(version_compare($module->getVersion(), $module_version[$module->getKey()]['version'])){
                            TFW_Registry::setSetting("module.update.".$module->getKey(), $module_version[$module->getKey()]["id"]);
                            TFW_Registry::setSetting("module.update.version.".$module->getKey(), $module_version[$module->getKey()]["version"]);
                            TFW_Registry::setSetting("module.update.description.".$module->getKey(), $module_version[$module->getKey()]["description"]);
                        }
                    }
                }
            }
        }

    }
