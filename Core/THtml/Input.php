<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    abstract class THtml_Input extends THtml_Base{
        protected $_regex      = "[^]{1,}";
        protected $_form_class = "";
        protected $_readonly   = false;

        /**
         * THtml_Input_Text constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($id, $class);
            $this->addAttributes([
                "name"        => $name,
                "value"       => "",
                "type"        => "none",
                "placeholder" => ""
            ]);
        }

        /**
         * @param string $value
         *
         * @return $this
         */
        public function setValue($value){
            $this->setAttribute("value", $value);
            return $this;
        }

        /**
         * @return string
         */
        public function getValue(){
            return $this->getAttribute("value");
        }

        /**
         * @param string $placeholder
         *
         * @return $this
         */
        public function setPlaceholder($placeholder){
            $this->setAttribute("placeholder", $placeholder." ...");
            return $this;
        }

        /**
         * @return string
         */
        public function getPlaceholder(){
            return $this->getAttribute("placeholder");
        }

        /**
         * @param string $regex
         *
         * @return $this
         */
        public function setRegex($regex){
            $this->_regex = $regex;
            return $this;
        }

        /**
         * @return string
         */
        public function getRegex(){
            return $this->_regex;
        }

        public function getFormClass(){
            return $this->_form_class;
        }

        public function setReadonly($r = true){
            $this->_readonly = $r;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            return "<input "
            .$this->_getAttributes()
            ." />";
        }

        /**
         * @return string
         */
        protected function _getAttributes(){
            $str = " id=\"$this->_id\" class=\"".implode(" ", $this->_classes)."\"";

            foreach($this->_attributes as $attr => $value)
                $str .= " $attr=\"$value\"";

            if($this->_regex)
                $str .= " regex-validation=\"$this->_regex\"";

            if($this->_readonly)
                $str .= " readonly";

            return $str;
        }
    }