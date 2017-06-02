<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 03/03/2017
     */
    class THtml_Button_Group extends THtml_Base{
        protected $_buttons = [];

        /**
         * THtml_Button_Group constructor.
         *
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($id = false, $class = ""){
            $class .= " btn-group";
            parent::__construct($id, $class);

            $this->addAttribute("role", "group");
        }

        /**
         * @param \THtml_Input_Button $btn
         *
         * @return $this
         */
        public function addButton(THtml_Input_Button $btn){
            if(get_class($btn) == "THtml_Input_Button_Dropdown")
                $btn->setAttribute("role", "group");
            $this->_buttons[] = $btn;
            return $this;
        }

        /**
         * @param string $value
         * @param string $action
         * @param string $class
         *
         * @return $this
         */
        public function add($value, $action, $class = "default"){
            $btn = new THtml_Input_Button($class);
            $btn
                ->setValue($value)
                ->setAction($action)
                ->setAttribute("role", "group");

            $this->_buttons[] = $btn;

            return $this;
        }

        /**
         * @param bool $v
         *
         * @return $this
         */
        public function setVertical($v = true){
            if($v){
                $this->removeClass("");
                $this->addClass("btn-group-vertical");
            }
            else{
                $this->removeClass("btn-group-vertical");
                $this->addClass("btn-group");
            }
            return $this;
        }

        /**
         * @return int
         */
        public function count(){
            return count($this->_buttons);
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<div".$this->_getAttributes().">";
            /**
             * @var THtml_Input_Button $btn
             */
            foreach($this->_buttons as $btn)
                $html .= $btn->get();
            $html .= "</div>";

            return $html;
        }
    }