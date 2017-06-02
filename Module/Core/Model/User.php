<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class Core_Model_User extends TDB_Model{
        protected $_table   = "app_users";
        protected $_root_as = "u";

        protected $_group_table = array("app_users_groups", "g");

        protected $_rights = "";

        protected function _addJoint(){
            $this
                ->addJoint(
                    $this->_group_table,
                    array("id" => "id_group"),
                    array(
                        "name"   => "group_name",
                        "rights" => "rights"
                    )
                );
        }

        /**
         * @param bool $onlyNew
         *
         * @return array|null
         * @throws TFW_Exception
         */
        public function getNotifications($onlyNew = false){
            $notifications = [];

            if($this->_isLoaded){
                $notifications = TFW_Registry::getModel("Core/User_Notification");
                $notifications->addWhere("id_recipient", $this->getId());
                if($onlyNew)
                    $notifications->addWhere("seen", 0);
                $notifications = $notifications->getList(false, true);
            }

            return $notifications;
        }

        protected function _afterLoad(){
            $this->_rights = base64_decode($this->rights);
        }

        /**
         * @param string $key
         *
         * @return bool
         */
        public function hasRight($key){
            if($this->_rights == "*" || strpos($this->_rights, $key) !== false)
                return true;
            else{
                $key = str_replace("*", "(.*?)", $key);
                $key = "/^(.*?)$key(.*?)/i";
                return  preg_match_all($key, $this->_rights);
            }
        }
    }