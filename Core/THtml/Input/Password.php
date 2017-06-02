<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Input_Password extends THtml_Input{
        protected $_form_class = "form-control";

        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "password");
        }

    }