<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */
    class THtml_Input_Submit extends THtml_Input_Button{
        /**
         * THtml_Input_Submit constructor.
         *
         * @param string      $style
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($style = "default", $id = false, $class = ""){
            parent::__construct($style, $id, $class);
            $this->setAttribute("type", "submit");
        }
    }