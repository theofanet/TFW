<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */

    class Core_Model_Setting extends TDB_Model{
        protected $root_table = "app_settings";
        protected $_root_as   = "s";

        protected $_select = ["setting_key", "setting_value"];
    }