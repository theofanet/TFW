<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/03/2017
     */
    class THtml_Input_Color extends THtml_Input_Text{

        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "color");
        }

    }