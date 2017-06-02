<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 11/04/2017
     */
    class THtml_Input_Editor extends THtml_Input_TextArea{
        protected $_height = 200;

        /**
         * @param int $h
         *
         * @return $this
         */
        public function setHeight($h){
            $this->_height = $h;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            TFW_Controller::addJs("Core/summernote.min.js");
            TFW_Controller::addCss("Core/summernote.css");

            $name = $this->getAttribute("name");

            $html = "<textarea ".$this->_getAttributes().">$this->_value</textarea>"
                   ."<input type=\"hidden\" name=\"$name\" value=\"$this->_value\" id=\"value_$this->_id\" />";

            $html .= "<script>"
                ."$('#$this->_id').summernote({height: $this->_height, callbacks: {onChange: function(contents){ $(\"#value_$this->_id\").val(contents); }}});"
                ."</script>";

            return $html;
        }

    }