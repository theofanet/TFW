<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/03/2017
     */
    class THtml_Image extends THtml_Base{
        protected $_src = NULL;

        /**
         * @param string $src
         *
         * @return $this
         */
        public function setSrc($src){
            $this->_src = $src;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            return "<img ".$this->_getAttributes()." src=\"$this->_src\" />";
        }
    }