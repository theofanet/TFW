<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class TFW_Event extends TFW_Abstract{

        private $_action;
        private $_args;

        /**
         * TFW_Event constructor.
         *
         */
        public function __construct(){
            $args = func_get_args();
            if(count($args) < 1) throw new TFW_Exception("Event should be called with event name at least");

            $this->_action = array_shift($args);
            $this->_args   = $args[0];
        }

        /**
         * @return string
         */
        public function getAction(){
            return $this->_action;
        }

        /**
         * @return null|mixed
         */
        public function getArgs(){
            return $this->_args;
        }

    }