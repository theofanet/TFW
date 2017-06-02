<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_Model_Module extends TDB_Model{
        protected $_table   = "app_modules";
        protected $_root_as = "m";
        protected $_idField = "module_key";
    }