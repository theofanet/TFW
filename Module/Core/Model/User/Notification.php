<?php

    /**
     * Created by Theo.
     */
    class Core_Model_User_Notification extends TDB_Model{
        protected $_table = "app_users_notifications";
        protected $_root_as = "n";

        protected function _beforeSave(){
            if(!$this->_isLoaded)
                $this->created_at = TFW_Registry::getHelper("Core/Time")->getDateTime();
            else
                $this->updated_at = TFW_Registry::getHelper("Core/Time")->getDateTime();
        }
    }