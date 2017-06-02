<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/03/2017
     */
    class THtml_Table_Filter_Date extends THtml_Table_Filter{

        protected $_timestamp = false;
        protected $operator   = array('>', '<', '>=', '<=', '!=');

        /**
         * THtml_Table_Filter_Date constructor.
         *
         * @param string $name
         * @param bool   $timestamp
         */
        public function __construct($name, $timestamp = false){
            $this->_timestamp = $timestamp;

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
         * @return bool|array
         */
        public function format($value){
            $parts = explode(' ', htmlspecialchars_decode($value));

            if(count($parts) == 2) {
                if(strstr($parts[1], ':')) {
                    $date1 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[1]);
                    $date2 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[1]) + 59;
                }
                else if(strtotime(str_replace('/', '-', $parts[0])) && strtotime(str_replace('/', '-', $parts[1]))) {
                    $date1 = strtotime(str_replace('/', '-', $parts[0]));
                    $date2 = strtotime(str_replace('/', '-', $parts[1])) + 82799;
                }
                else {
                    $date = strtotime(str_replace('/', '-', $parts[1]));
                    if($date && in_array($parts[0], $this->operator))
                        return array($this->_timestamp ? $date : TFW_Registry::getHelper('Core/Time')->getDateTime($date), $parts[0]);

                    return false;
                }
            }
            else if(count($parts) == 3) {
                if(strstr($parts[1], '-')) {
                    $date1 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[2]);
                    $date2 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[2]) + 59;
                }
                else if(strstr($parts[2], ':')) {
                    $date = strtotime(str_replace('/', '-', $parts[1]).' '.$parts[2]);
                    if($date && in_array($parts[0], $this->operator))
                        return array($this->_timestamp ? $date : TFW_Registry::getHelper('Core/Time')->getDateTime($date), $parts[0]);

                    return false;
                }
                else{
                    $date1 = strtotime(str_replace('/', '-', $parts[0]));
                    $date2 = strtotime(str_replace('/', '-', $parts[2]));
                }
            }
            else if(count($parts) == 4) {
                if(strstr($parts[3], ':') && strstr($parts[2], '-')) {
                    $date = strtotime(str_replace('/', '-', $parts[1]).' '.$parts[3]);
                    if($date && in_array($parts[0], $this->operator))
                        return array($this->_timestamp ? $date : TFW_Registry::getHelper('Core/Time')->getDateTime($date), $parts[0]);

                    return false;
                }
                else{
                    $date1 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[1]);
                    $date2 = strtotime(str_replace('/', '-', $parts[2]).' '.$parts[3]);
                }
            }
            else if(count($parts) == 6) {
                $date1 = strtotime(str_replace('/', '-', $parts[0]).' '.$parts[2]);
                $date2 = strtotime(str_replace('/', '-', $parts[3]).' '.$parts[5]);
            }
            else{
                $date1 = strtotime(str_replace('/', '-', $value));
                $date2 = strtotime(str_replace('/', '-', $value)) + 82799;
            }

            $dates = [
                ($this->_timestamp ? $date1 : TFW_Registry::getHelper('Core/Time')->getDateTime($date1)),
                ($this->_timestamp ? $date2 : TFW_Registry::getHelper('Core/Time')->getDateTime($date2))
            ];

            return array($dates, ' BETWEEN ');
        }
    }