<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Input_Text extends THtml_Input{
        protected $_form_class = "form-control";

        protected $_side = array(
            'right' => false,
            'left'  => false
        );

        protected $_onEnter  = false;
        protected $_onUpdate = false;
        protected $_onTab    = false;

        protected $_typeAhead = NULL;

        /**
         * THtml_Input_Text constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($name, $id, $class);
            $this->setAttribute("type", "text");
        }

        /**
         * @param string $icon
         * @param string $side
         *
         * @return \THtml_Input_Text
         */
        public function setIcon($icon, $side = 'left'){
            return $this->setSideContent('<i class="fa '.$icon.'"></i>', $side);
        }

        /**
         * @param string $content
         * @param string $side
         * @param string $type
         *
         * @return $this
         */
        public function setSideContent($content, $side = 'left', $type = 'addon'){
            if($side == 'left' || $side == 'right')
                $this->_side[$side] = '<span class="input-group-'.$type.'">'.$content.'</span>';

            return $this;
        }

        /**
         * @param \THtml_Input_Button $button
         * @param string              $side
         *
         * @return $this
         */
        public function setSideButton(THtml_Input_Button $button, $side = 'left'){
            $button->addClass("input-side-button");
            if($side == 'left' || $side == 'right')
                $this->_side[$side] = '<span class="input-group-btn">'.$button->get().'</span>';

            return $this;
        }

        /**
         * @param string $data
         *
         * @return $this
         */
        public function addTypeAhead($data){
            if(!$this->_typeAhead)
                $this->_typeAhead = array();

            $this->_typeAhead[] = $data;
            return $this;
        }

        /**
         * @param string $func
         *
         * @return $this
         */
        public function onEnter($func){
            $this->_onEnter = $func;
            return $this;
        }

        /**
         * @param string $func
         *
         * @return $this
         */
        public function onTab($func){
            $this->_onTab = $func;
            return $this;
        }

        /**
         * @param string $func
         *
         * @return $this
         */
        public function onUpdate($func){
            $this->_onUpdate = $func;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $result = '';

            if($this->_side['left'] || $this->_side['right']){
                $result .= '<div class="input-group">';
                if($this->_side['left'])
                    $result .= $this->_side['left'];
            }

            $result .= "<input "
                .$this->_getAttributes()
                ." />";

            if($this->_side['left'] || $this->_side['right']){
                if($this->_side['right'])
                    $result .= $this->_side['right'];
                $result .= '</div>';
            }


            if($this->_typeAhead || $this->_onEnter ||$this->_onUpdate || $this->_onTab){
                $result .= "<script>";

                if($this->_typeAhead){
                    //Adding JS component
                    TFW_Controller::addJs("Core/typeahead.jquery.min.js");

                    $result .= "$('#$this->_id').typeahead({hint:true,highlight:true,minLength:1}, {"
                        ."source: substringMatcher(".json_encode($this->_typeAhead).")"
                        ."});";
                }

                if($this->_onEnter)
                    $result .= "$('#$this->_id').onEnter($this->_onEnter);";
                if($this->_onTab)
                    $result .= "$('#$this->_id').onTab($this->_onTab);";
                if($this->_onUpdate)
                    $result .= "$('#$this->_id').onUpdate($this->_onUpdate);";

                $result .= "</script>";
            }


            return $result;
        }

    }