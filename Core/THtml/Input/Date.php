<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/03/2017
     */
    class THtml_Input_Date extends THtml_Input_Text{
        protected $_form_class = "form-control";

        /**
         * THtml_Input_Date constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "date");
        }
    }