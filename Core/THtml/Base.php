<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    abstract class THtml_Base extends TFW_Abstract{
        protected $_id;
        protected $_classes = array();

        protected $_attributes = array();

        /**
         * THtml_Base constructor.
         *
         * @param bool|string $class
         * @param bool|string $id
         */
        public function __construct($id = false, $class = ""){
            if(!$id)
                $id = uniqid();

            $this->_id = $id;

            if($class)
                $this->addClass($class);
        }

        /**
         * @param string $id
         *
         * @return $this
         */
        public function setId($id){
            $this->_id = $id;
            return $this;
        }

        /**
         * @return bool|string
         */
        public function getId(){
            return $this->_id;
        }

        /**
         * @return array
         */
        public function getClasses(){
            return $this->_classes;
        }

        /**
         * @param string $class
         *
         * @return $this
         */
        public function addClass($class){
            $classes = explode(" ", $class);

            foreach($classes as $c){
                if(!isset($this->_classes[$c]))
                    $this->_classes[$c] = $c;
            }

            return $this;
        }

        /**
         * @param string $class
         *
         * @return $this
         * @throws \TFW_Exception
         */
        public function removeClass($class){
            $classes = explode(" ", $class);
            if(count($classes) > 1) throw new TFW_Exception(get_class($this)." - removeClass : more than one class");

            if(isset($this->_classes[$class]))
                unset($this->_classes[$class]);

            return $this;
        }

        /**
         * @param array $attributes
         *
         * @return $this
         */
        public function addAttributes($attributes){
            foreach($attributes as $attribute => $value)
                $this->addAttribute($attribute, $value);

            return $this;
        }

        /**
         * @param string $attribute
         * @param string $value
         *
         * @return $this
         */
        public function addAttribute($attribute, $value){
            $this->_attributes[$attribute] = $value;
            return $this;
        }

        /**
         * @param string $attribute
         * @param string $value
         *
         * @return $this
         */
        public function setAttribute($attribute, $value){
            return $this->addAttribute($attribute, $value);
        }

        /**
         * @param string $attribute
         *
         * @return $this
         */
        public function removeAttribute($attribute){
            if(isset($this->_attributes[$attribute]))
                unset($this->_attributes[$attribute]);
            return $this;
        }

        /**
         * @param string $attribute
         *
         * @return bool|string
         */
        public function getAttribute($attribute){
            if(isset($this->_attributes[$attribute]))
                return $this->_attributes[$attribute];

            return false;
        }

        /**
         * @return string
         */
        abstract public function get();

        /**
         * @return string
         */
        protected function _getAttributes(){
            $str = " id=\"$this->_id\" class=\"".implode(" ", $this->_classes)."\"";

            foreach($this->_attributes as $attr => $value)
                $str .= " $attr=\"$value\"";

            return $str;
        }

        public function show(){
            echo $this->get();
        }

    }