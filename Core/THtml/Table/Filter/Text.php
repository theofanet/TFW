<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class THtml_Table_Filter_Text extends THtml_Table_Filter{

        /**
         * THtml_Table_Filter_Text constructor.
         *
         * @param string $name
         */
        public function __construct($name){
            parent::__construct($name);

            $this->_element = new THtml_Input_Text("filter:$name", false, "form-control table_filter_input");
            $this->_element
                ->setPlaceholder($this->__("Filter"));
        }

        /**
         * @param string $data
         *
         * @return $this
         */
        public function setValue($data){
            if(!is_string($data))
                return $this;

            $this->_element->setValue($data);

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
            if(TFW_Registry::getSetting('tables.filters.text.mode') == 2)
                $value = '%'.str_replace(' ', '%', $value).'%';

            return array($value, ' LIKE ');
        }

    }