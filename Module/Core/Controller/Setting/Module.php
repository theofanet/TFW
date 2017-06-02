<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 03/03/2017
     */
    class Core_Controller_Setting_Module extends TFW_Controller{

        public function updateCore(){
            $version_id = TFW_Registry::getSetting("core.update");

            if($version_id){
                $rest = new TFW_Rest(TFW_Core::getUpdaterUrl());
                $rest
                    ->addHeader("Authorization", "Basic dGVzdGVyOnRlc3Rpc2xpZmU=")
                    ->addHeader("Api-Key", "5lf93rEm7baVAw026481");

                $data = $rest->get("/download/core/$version_id");

                if($data){
                    $file = new TFile_Zip();
                    $file->addContent($data);

                    if($file->extractTo(ROOT_PATH.TFW_IO::DS)){
                        TFW_Registry::removeSetting("core.update");
                        TFW_Registry::removeSetting("core.update.version");
                        TFW_Registry::removeSetting("core.update.description");

                        $file->remove();

                        $this->TQuery_Result();
                    }
                    else{
                        $file->remove();
                        $this->TQuery_Error($this->__("Unable to extract archive"));
                    }
                }
                else
                    $this->TQuery_Error($this->__("Unable to download archive"));
            }
            else
                $this->TQuery_Error($this->__("Unable to retrieve version to download"));
        }

        public function update(){
            $module_key = $this->module_key;

            if($module_key){
                $module = TFW_Registry::getModel("Core/Module");
                $module->load($module_key);

                if($module->isLoaded()){

                    $version_id = TFW_Registry::getSetting("module.update.".$module_key);

                    if($version_id){
                        $rest = new TFW_Rest(TFW_Core::getUpdaterUrl());
                        $rest
                            ->addHeader("Authorization", "Basic dGVzdGVyOnRlc3Rpc2xpZmU=")
                            ->addHeader("Api-Key", "5lf93rEm7baVAw026481");

                        $data = $rest->get("/download/module/$version_id");

                        if($data){
                            $file = new TFile_Zip();
                            $file->addContent($data);

                            if($file->extractTo(ROOT_PATH.TFW_IO::DS."Module".TFW_IO::DS)){
                                TFW_Registry::removeSetting("module.update.".$module_key);
                                TFW_Registry::removeSetting("module.update.version.".$module_key);
                                TFW_Registry::removeSetting("module.update.description.".$module_key);

                                $file->remove();

                                $this->TQuery_Result();
                            }
                            else{
                                $file->remove();
                                $this->TQuery_Error($this->__("Unable to extract archive"));
                            }
                        }
                        else
                            $this->TQuery_Error($this->__("Unable to download archive"));
                    }
                    else
                        $this->TQuery_Error($this->__("Unable to retrieve version to download"));
                }
                else
                    $this->TQuery_Error($this->__("Unable to load module %s", $module_key));
            }
            else
                $this->TQuery_Error($this->__("Missing module key"));
        }

    }