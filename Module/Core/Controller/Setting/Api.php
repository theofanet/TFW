<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class Core_Controller_Setting_Api extends TFW_Controller{
        /**
         * @param int $id_key
         */
        public function getKeyModal($id_key){
            if(!$id_key)
                $this->TQuery_Error($this->__("Missing key ID"));

            $key = TFW_Registry::getModel("Core/Api_Key");
            $key->load($id_key);

            if($key->isLoaded())
                $this->TQuery_Result($this->getBlock("Core/Settings_Api_Edit", ["api_key" => $key]));
            else
                $this->TQuery_Error($this->__("Unable to load API key %s", $id_key));
        }

        /**
         * Method to save API key
         * If $id_key is false or not found in table,
         * key will be created
         *
         * @param bool|int $id_key
         */
        public function saveApiKey($id_key = false){
            if((!$id_key && !TFW_Core::getUser()->hasRight("Core:settings:api:create"))
                || ($id_key && !TFW_Core::getUser()->hasRight("Core:settings:api:update"))){
                self::trigger404();
                exit;
            }

            $key_label  = $this->key_label;
            $key_group  = $this->key_group;
            $key_active = $this->key_active;
            $key_value  = false;

            if($key_label && $key_group){
                $key = TFW_Registry::getModel("Core/Api_Key");

                if($id_key)
                    $key->load($id_key);

                if(!$key->isLoaded())
                    $key_value = TFW_Registry::getHelper("Core/Text")->generatePass();

                $key->label    = $key_label;
                $key->id_group = $key_group;
                $key->active   = ($key_active ? 1 : 0);
                if($key_value)
                    $key->value = $key_value;

                if($key->save())
                    TFW_Flash::addSuccess($this->__("API key saved"));
                else
                    TFW_Flash::addError($this->__("Unable to save API key"));
            }
            else
                TFW_Flash::addError($this->__("Missing data"));

            self::goToRoute("/settings/application/api");
        }
    }