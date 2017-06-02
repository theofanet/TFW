<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */
    class Core_Model_User_Group extends TDB_Model{
        protected $_table   = "app_users_groups";
        protected $_root_as = "g";

        /**
         * Used to apply base64 decode for
         * rights
         *
         * @param string $name
         *
         * @return null|string
         */
        public function __get($name){
            $result = parent::__get($name);
            if($name == "rights")
                $result = base64_decode($result);
            return $result;
        }
    }