<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 20/03/2017
     */
    class THtml_Div extends THtml_Base{
        protected $_content = "";

        /**
         * @param string $c
         *
         * @return $this
         */
        public function setContent($c){
            $this->_content = $c;
            return $this;
        }

        /**
         * @param string $c
         *
         * @return $this
         */
        public function addContent($c){
            $this->_content .= $c;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $result = "<div ".$this->_getAttributes().">"
                .$this->_content
                ."</div>";

            return $result;
        }
    }