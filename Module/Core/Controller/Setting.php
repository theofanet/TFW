<?php

    /**
     * Created by Theo.
     */
    class Core_Controller_Setting extends TFW_Controller{
        protected $_rights = [
            "index" => "Core:settings:see",
            "save"  => "Core:settings:update",
            "newLanguage" => "Core:settings:update",
            "restartCron" => "Core:settings:cron:restart"
        ];


        protected function _constructSettingsArray(){
            $rawSettings = TFW_Registry::getRegistered("settings");

            $settings = array();
            foreach($rawSettings as $moduleKey => $set){

                foreach($set as $group => $groupData){

                    if(!isset($groupData['items']))
                        $groupData['items'] = array();
                    if(!isset($groupData['position']))
                        $groupData['position'] = 1000000;

                    if(!isset($settings[$group]))
                        $settings[$group] = $groupData;
                    else{
                        if(!isset($settings[$group]['label']) && isset($groupData['label']))
                            $settings[$group]['label'] = $groupData['label'];
                        if(!isset($settings[$group]['position']) && isset($groupData['position']))
                            $settings[$group]['position'] = $groupData['position'];
                        if(!isset($settings[$group]['items']))
                            $settings[$group]['items'] = array();

                        foreach($groupData['items'] as $item => $itemData){
                            if(!isset($itemData['fields']))
                                $itemData['fields'] = array();
                            if(!isset($itemData['position']))
                                $itemData['position'] = 1000000;

                            if(!isset($settings[$group][$item]))
                                $settings[$group]['items'][$item] = $itemData;
                            else{
                                if(!isset($settings[$group]['items'][$item]['label']) && isset($itemData['label']))
                                    $settings[$group]['items'][$item]['label'] = $itemData['label'];
                                if(!isset($settings[$group]['items'][$item]['position']) && isset($itemData['position']))
                                    $settings[$group]['items'][$item]['position'] = $itemData['position'];

                                foreach($itemData['fields'] as $field)
                                    $settings[$group]['items'][$item]['fields'][] = $field;
                            }
                        }
                    }

                }

            }

            uasort($settings, $this->_sort_by('position'));
            foreach($settings as $k => $m){
                if(isset($m['items']))
                    uasort($settings[$k]['items'], $this->_sort_by('position'));
            }

            return $settings;
        }

        private function _sort_by($key){
            return function($a, $b) use($key){
                if(!isset($a[$key]))
                    $a[$key] = 1000000;
                if(!isset($b[$key]))
                    $b[$key] = 1000000;

                if($a[$key] == $b[$key])
                    return 0;

                return ($a[$key] < $b[$key]) ? -1 : 1;
            };
        }

        public function index($group = "application", $item = "environment"){
            $settingsData = $this->_constructSettingsArray();

            $this->setSideBlock("Core/Settings_Menu", [
                "settings"       => $settingsData,
                "selected_group" => $group,
                "selected_item"  => $item
            ]);

            $this->render("Core/Settings", [
                "settingsData" => $settingsData,
                "group" => $group,
                "item"  => $item
            ]);
        }

        public function save(){
            $configs_var = $this->configs_var;

            if($configs_var && is_array($configs_var)){
                foreach($configs_var as $key => $value)
                    TFW_Registry::setSetting($key, $value);

                TFW_Flash::addSuccess($this->__("Settings saved"));
            }
            else
                TFW_Flash::addError($this->__("Missing data"));

            $route = "/settings";
            if($this->setting_group)
                $route .= "/$this->setting_group";
            if($this->setting_item)
                $route .= "/$this->setting_item";

            self::goToRoute($route);
        }

        public function newLanguage(){
            $name = $this->language_name;
            $code = strtolower($this->language_code);

            if($name && $code){
                $model = TFW_Registry::getModel("Core/Language");
                $model->addWhere("lang_code", $code);
                $model->load();

                if(!$model->isLoaded()){
                    $model->lang_code = $code;
                    $model->lang_name = $name;

                    if($model->save())
                        TFW_Flash::addSuccess($this->__("Language created"));
                    else
                        TFW_Flash::addError($this->__("Unable to save language"));
                }
                else
                    TFW_Flash::addError($this->__("A language with the same code already exsits"));
            }
            else
                TFW_Flash::addError($this->__("Missing data"));

            self::goToRoute("/settings/application/lang");
        }

        public function restartCron(){
            TFW_Registry::setSetting("cron.running", 0);
            $this->TQuery_Result();
        }

        public function lunchCron(){
            $elements = TFW_Registry::getPostData("elements");
            if($elements && is_array($elements)){
                foreach($elements as $cron_id){
                    $cron = TFW_Registry::getModel("Core/Cron");
                    $cron->load($cron_id);

                    if($cron->isLoaded()){
                        $cron->next_execution_time = TFW_Registry::getHelper("Core/Time")->getDateTime();
                        if(!$cron->save())
                            TFW_Flash::addError($this->__("Unable to save cron %s", $cron_id));
                    }
                    else
                        TFW_Flash::addError($this->__("Unable to load cron %s", $cron_id));
                }
            }

            self::goToRoute("/settings/application/cron");
        }

    }