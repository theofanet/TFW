<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 06/04/2017
     */
    class THtml_Uploader extends THtml_Base{
        protected $_filesBlock;
        protected $_data;
        protected $_JSCallBack;
        protected $_route;

        /**
         * THtml_Uploader constructor.
         *
         * @param string      $route
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($route, $id = false, $class = ""){
            $class .= ' uploadBox';
            parent::__construct($id, $class);

            $this->_route      = $route;
            $this->_data       = array();
            $this->_filesBlock = false;
            $this->_JSCallBack = NULL;
        }

        /**
         * @param string $id
         *
         * @return $this
         */
        public function setFileBlock($id){
            $this->_filesBlock = $id;
            return $this;
        }

        /**
         * @param string $key
         * @param string $value
         *
         * @return $this
         */
        public function addData($key, $value){
            $this->_data[$key] = $value;
            return $this;
        }

        /**
         * @param string $js
         *
         * @return $this
         */
        public function setJSCallBack($js){
            $this->_JSCallBack = $js;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            TFW_Controller::addJs("Core/jquery.filedrop.js");
            TFW_Controller::addJs("Core/TUploader.js");

            $html = '<div '.$this->_getAttributes().'>'
                .'<span class="message">'
                .'<i class="fa fa-cloud-upload cloud_icon"></i><br />'
                .$this->__('Drop files here to upload them')
                .'</span>'
                .'</div>';

            $html .= "<script>"
                ."var uploader_$this->_id = new TUploader('#$this->_id', '$this->_route');";

            if(count($this->_data))
                $html .= "uploader_$this->_id.addData(".json_encode($this->_data).");";
            if($this->_JSCallBack)
                $html .= "uploader_$this->_id.setCallback($this->_JSCallBack);";

            $html .= "uploader_$this->_id.make();"
                ."</script>";

            return $html;
        }
    }