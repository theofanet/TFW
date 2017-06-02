<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 22/03/2017
     */
    class THtml_Input_Hidden extends THtml_Input{

        /**
         * THtml_Input_Hidden constructor.
         *
         * @param string      $name
         * @param string      $value
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $value, $id = false, $class = ""){
            parent::__construct($name, $id, $class);

            $this
                ->setValue($value)
                ->setAttribute("type", "hidden");
        }

    }