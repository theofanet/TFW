<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    abstract class THtml_Table_Filter extends TFW_Abstract{
        protected $_element  = null;
        protected $_name     = null;
        protected $_built    = false;
        protected $_position = 0;

        /**
         * THtml_Table_Filter constructor.
         *
         * @param string $name
         */
        public function __construct($name){
            $this->_name = $name;
        }

        /**
         * @param array $data
         *
         * @return $this
         */
        abstract public function setValue($data);

        /**
         * @return int
         */
        public function getPosition(){
            return $this->_position;
        }

        /**
         * @param int $position
         */
        public function setPosition($position){
            $this->_position = $position;
        }

        /**
         * @return string
         */
        public function getName(){
            return $this->_name;
        }

        /**
         * @return null|THtml_Input
         */
        public function getElement(){
            return $this->_element;
        }

        /**
         * @param mixed $value
         *
         * @return mixed
         */
        public function format($value){
            return $value;
        }

        /**
         * @return string
         */
        abstract public function get();
    }