<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */
    class THtml_Input_Checkbox extends THtml_Input{
        private $_label   = false;
        private $_checked = false;

        /**
         * THtml_Input_Checkbox constructor.
         *
         * @param bool|string $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "checkbox");
            $this->setAttribute("value", 1);
        }

        /**
         * @param string $label
         *
         * @return $this
         */
        public function setLabel($label){
            $this->_label = $label;
            return $this;
        }

        /**
         * @param string $placeholder
         *
         * @return \THtml_Input_Checkbox
         */
        public function setPlaceholder($placeholder){
            return $this->setLabel($placeholder);
        }

        /**
         * @param bool $checked
         *
         * @return $this
         */
        public function setChecked($checked = true){
            $this->_checked = $checked;
            return $this;
        }

        /**
         * @param string $value
         *
         * @return \THtml_Input_Checkbox
         */
        public function setValue($value){
            return $this->setChecked($value);
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<div class=\"checkbox\">"
                ."<label>"
                ."<input ".$this->_getAttributes()." ".($this->_checked ? "checked" : "")."/> "
                .$this->_label
                ."</label>"
                ."</div>";

            return $html;
        }
    }