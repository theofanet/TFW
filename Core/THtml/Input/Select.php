<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    class THtml_Input_Select extends THtml_Input{
        private $_data     = [];
        private $_selected = null;
        private $_default  = "Select";

        protected $_form_class = "form-control";

        /**
         * THtml_Input_Select constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->addAttribute("onchange", "");
        }

        /**
         * @param string $key
         * @param string $value
         * @param array  $args
         *
         * @return $this
         */
        public function add($key, $value, $args = []){
            $this->_data[] = [$key, $value, $args];
            return $this;
        }

        /**
         * @param string $attribute
         * @param string $value
         *
         * @return $this
         */
        public function setAttribute($attribute, $value){
            if($attribute == "onchange")
                $value = "selectPlaceholderCheck(this);$value";

            return parent::setAttribute($attribute, $value);
        }

        /**
         * @param string $attribute
         * @param string $value
         *
         * @return $this
         */
        public function addAttribute($attribute, $value){
            if($attribute == "onchange")
                $value = "selectPlaceholderCheck(this);$value";

            return parent::addAttribute($attribute, $value);
        }

        /**
         * @param bool|string $label
         *
         * @return $this
         */
        public function setDefaultLabel($label){
            $this->_default = $label;
            return $this;
        }

        /**
         * @param string $placeholder
         *
         * @return \THtml_Input_Select
         */
        public function setPlaceholder($placeholder){
            return $this->setDefaultLabel($placeholder." ...");
        }

        /**
         * @param string $item
         *
         * @return $this
         */
        public function setSelected($item){
            $this->_selected = $item;
            return $this;
        }

        /**
         * @param string $value
         *
         * @return \THtml_Input_Select
         */
        public function setValue($value){
            return $this->setSelected($value);
        }

        /**
         * @return int
         */
        public function count(){
            return count($this->_data);
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
                $str .= " disabled";

            return $str;
        }

        /**
         * @return string
         */
        public function get(){
            $options = "";
            $select_selected = false;

            foreach($this->_data as list($key, $value, $args)){
                $selected = "";
                if($this->_selected !== null && $this->_selected == $key){
                    $selected = "selected";
                    $select_selected = true;
                }

                $args_ = "";
                if(count($args)){
                    foreach($args as $k => $v)
                        $args_ .= " $k='$v'";
                }

                $options .= "<option value=\"$key\" $args_ $selected>$value</option>";
            }

            if(!$select_selected)
                $this->addClass("select-not-selected");

            $html = "<select".$this->_getAttributes().">";
            if($this->_default !== false)
                $html .= "<option class=\"select-default-value\" value=\"\">".$this->__($this->_default)."</option>";
            $html .= $options;
            $html .= "</select>";

            return $html;
        }
    }