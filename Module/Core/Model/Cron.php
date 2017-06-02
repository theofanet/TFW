<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_Model_Cron extends TDB_Model{
        protected $_table = "app_cron_tasks";
        protected $_root_as = "c";

        public function getData(){
            return $this->_data;
        }
    }