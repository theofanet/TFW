<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */
    class THtml_Table_Filter_Select extends THtml_Table_Filter{
        private $_data = [];

        /**
         * THtml_Table_Filter_Select constructor.
         *
         * @param string $name
         * @param array  $data
         */
        public function __construct($name, $data = array()){
            parent::__construct($name);

            $this->_element = new THtml_Input_Select("filter:$name", false, "form-control table_filter_input");
            $this->_element
                ->setDefaultLabel($this->__("Filter")." ...");

            $this->_data = $data;
            foreach($this->_data as $key => $value)
                $this->_element->add($key, $value);
        }

        /**
         * @param string $data
         *
         * @return $this
         */
        public function setValue($data){
            $this->_element->setSelected($data);

            $this->_built = true;

            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            return $this->_element->get();
        }

        /**
         * @param string $value
         *
         * @return array
         */
        public function format($value){
            return $value;
        }
    }