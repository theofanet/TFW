<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */
    class THtml_Input_Button extends THtml_Input{
        protected $_regex = false;
        protected $_style     = "";

        /**
         * THtml_Input_Button constructor.
         *
         * @param string      $style
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($style = "default", $id = false, $class = ""){
            $class .= " btn btn-".$style;

            parent::__construct("", $id, $class);

            $this
                ->removeAttribute("name")
                ->removeAttribute("placeholder")
                ->setAttribute("type", "button");

            $this->_style = $style;
        }

        /**
         * @param bool $d
         *
         * @return $this
         */
        public function setDisabled($d = true){
            $this->setAttribute("disabled", $d ? "true": "false");
            return $this;
        }

        public function setStyle($style){
            $this->removeClass("btn-$this->_style");
            $this->addClass("btn-$style");
            $this->_style = $style;
            return $this;
        }

        /**
         * @param string $js
         *
         * @return $this
         */
        public function setAction($js){
            $this->setAttribute("onclick", $js);
            return $this;
        }
    }