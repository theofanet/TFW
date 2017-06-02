<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class Core_Model_Api_Key extends TDB_Model{
        protected $_table = "app_api_keys";
        protected $_root_as = "k";

        protected $_select = [
            "k.value AS value",
            "k.label AS label",
            "k.id_group AS id_group",
            "k.active AS active",
            "k.created_at AS created_at"
        ];

        protected $_groups_table = ["app_users_groups", "g"];

        protected function _addJoint(){
            $this
                ->addJoint(
                    $this->_groups_table,
                    ["id" => "id_group"],
                    [
                        "name"   => "group_name",
                        "rights" => "rights"
                    ]
                );
        }

        protected function _beforeSave(){
            if(!$this->isLoaded())
                $this->created_at = TFW_Registry::getHelper("Core/Time")->getDateTime();
        }

        /**
         * @param string $key
         *
         * @return bool
         */
        public function hasRight($key){
            if($this->rights == "*" || strpos($this->rights, $key) !== false)
                return true;
            return false;
        }
    }