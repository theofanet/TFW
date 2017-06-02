<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 03/03/2017
     */
    class THtml_Button_Toolbar extends THtml_Base{
        protected $_groups = [];
        protected $_small  = false;

        /**
         * THtml_Button_Toolbar constructor.
         *
         * @param bool        $small
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($small = false, $id = false, $class = ""){
            $class .= " btn-toolbar";
            parent::__construct($id, $class);
            $this->addAttribute("role", "toolbar");
            $this->_small = $small;
        }

        /**
         * @param \THtml_Button_Group $g
         *
         * @return $this
         */
        public function addGroup(THtml_Button_Group $g){
            if($this->_small)
                $g->addClass("btn-group-xs");

            $this->_groups[] = $g;
            return $this;
        }

        /**
         * @param \THtml_Input_Button $b
         *
         * @return $this
         */
        public function addButton(THtml_Input_Button $b){
            $g = new THtml_Button_Group();

            if($this->_small){
                $g->addClass("btn-group-xs");
                $b->addClass("btn-xs");
            }

            $g->addButton($b);
            $this->_groups[] = $g;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<div".$this->_getAttributes().">";
            /**
             * @var THtml_Button_Group $g
             */
            foreach($this->_groups as $g)
                $html .= $g->get();
            $html .= "</div>";

            return $html;
        }

    }