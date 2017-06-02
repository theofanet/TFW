<?php

    class TFile_Text extends TFile_Base{
        protected $_valid_extensions = array('txt');
        protected $_content = '';

        /**
         * @param string $content
         */
        public function setContent($content){
            $this->_content = $content;
        }

        /**
         * Save file with content
         */
        public function save(){
            if(!$this->_file || $this->_opened_mode != self::OPEN_WRITE)
                $this->open(self::OPEN_WRITE);

            fwrite($this->_file, $this->_content);

            $this->_after_save();
            $this->close();
        }
    }