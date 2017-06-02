<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Grid extends THtml_Base{
        private $_rows = array();
        private $_currentRow = array();

        public function __construct(){
            parent::__construct();
        }

        /**
         * @param string|THtml_Base $data
         * @param int               $size
         *
         * @return $this
         */
        public function addColumn($data, $size = 12){
            $data_str = "";
            if(is_string($data))
                $data_str = $data;
            else if($data instanceof THtml_Base)
                $data_str = $data->get();

            $this->_currentRow[] = [$data_str, $size];

            return $this;
        }

        /**
         * @return $this
         */
        public function addCurrentRow(){
            $this->_rows[] = $this->_currentRow;
            $this->_currentRow = [];
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "";

            foreach($this->_rows as $raw){
                $html .= "<div class=\"raw\">";
                foreach($raw as list($data, $size))
                    $html .= "<div class=\"col-sm-$size\">$data</div>";
                $html .= "</div>";
            }

            return $html;
        }

    }