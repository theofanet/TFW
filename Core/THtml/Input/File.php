<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 27/02/2017
     */
    class THtml_Input_File extends THtml_Input{
        /**
         * THtml_Input_File constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "file");
        }
    }