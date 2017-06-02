<?php

    /**
     * TFrameWork2
     *
     * User: theo
     * Date: 17/02/2017
     */
    class THtml_Input_TextArea extends THtml_Input{
        protected $_value = "";
        protected $_form_class = "form-control";

        /**
         * @param string $value
         *
         * @return $this
         */
        public function setValue($value){
            $this->_value = $value;
            return $this;
        }

        /**
         * @return string
         */
        public function getValue(){
            return $this->_value;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<textarea".$this->_getAttributes().">"
                .$this->_value
                ."</textarea>";

            return $html;
        }
    }