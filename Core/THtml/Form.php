<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Form extends THtml_Base{

        private $_elements   = array();
        private $_horizontal = true;

        private $_method = "POST";
        private $_action = "";

        private $_hidden = [];

        private $_showActions = true;
        private $_buttons = [
            "submit" => [
                "label" => "Validate",
                "class" => "success"
            ]
        ];

        /**
         * THtml_Form constructor.
         *
         * @param string      $action
         * @param string      $class
         * @param bool|string $id
         */
        public function __construct($action = "", $id = false, $class = ""){
            $class .= " form-horizontal";
            parent::__construct($id, $class);

            $this->_action = $action;
            $this->setActive();
        }

        /**
         * @param bool $active
         *
         * @return $this
         */
        public function setActive($active = true){
            if($active)
                $this->setAttribute("onsubmit", 'return Form.validate(\''.$this->_id.'\');');
            else{
                $this->showActions(false);
                $this->setAttribute("onsubmit", "return false");
            }
            return $this;
        }

        /**
         * @param string $action
         *
         * @return $this
         */
        public function setAction($action){
            $this->_action = $action;
            return $this;
        }

        /**
         * @param bool $h
         *
         * @return $this
         */
        public function setHorizontal($h = true){
            $this->_horizontal = $h;

            if($this->_horizontal)
                $this->addClass("form-horizontal");
            else
                $this->removeClass("form-horizontal");

            return $this;
        }

        /**
         * @param bool $s
         *
         * @return $this
         */
        public function showActions($s = true){
            $this->_showActions = $s;
            return $this;
        }

        /**
         * @param bool $validate
         *
         * @return $this
         */
        public function setValidation($validate = true){
            $this->setAttribute("onsubmit", ($validate ? 'return Form.validate(\''.$this->_id.'\');' : ''));
            return $this;
        }

        /**
         * @param string      $label
         * @param \THtml_Base $element
         * @param bool        $required
         * @param array       $options
         *
         * @return $this
         */
        public function addElement($label, THtml_Base $element, $required = true, $options = array()){
            $options["required"] = $required;

            if($element instanceof THtml_Input)
                $element->addClass($element->getFormClass());

            $this->_elements[] = [
                $label,
                $element,
                $options,
                false
            ];

            return $this;
        }

        /**
         * @param string $html
         * @param array  $options
         *
         * @return $this
         */
        public function addHtml($html, $options = array()){
            $this->_elements[] = [
                false,
                false,
                $options,
                $html
            ];

            return $this;
        }

        /**
         * @param string      $name
         * @param string      $value
         * @param bool|string $id
         *
         * @return $this
         */
        public function addHidden($name, $value, $id = false){
            $this->_hidden[] = new THtml_Input_Hidden($name, $value, $id);
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<form method=\"$this->_method\" action=\"$this->_action\" enctype=\"multipart/form-data\"".$this->_getAttributes().">";

            /**
             * @var THtml_Input_Hidden $hidden
             */
            foreach($this->_hidden as $hidden)
                $html .= $hidden->get();

            /**
             * @var THtml_Base $element
             */
            foreach($this->_elements as list($label, $element, $options, $lineHtml)){
                $html .= "<div class=\"form-group\" required=\"".($options["required"] ? "true" : "false")."\">";

                if($label){
                    if($options["required"])
                        $label .= " *";
                    $html .= "<label for=\"".$element->getId()."\" class=\"col-sm-3 control-label\">$label</label>";
                }

                $html .= ($label !== false && $this->_horizontal ? "<div class=\"".($label ? "" : "col-sm-offset-3 ")."col-sm-".($label !== false ? "9" : "12")."\">" : "")
                    .($element ? $element->get() : $lineHtml)
                    .($label !== false && $this->_horizontal ? "</div>" : "");

                $html .= "</div>";
            }

            /*
             * Form actions
             */
            if($this->_showActions){
                $html .= "<div class=\"form-group\"><div class=\"col-sm-offset-3 col-sm-9\">"
                    ."<button type=\"submit\" class=\"btn btn-".$this->_buttons["submit"]["class"]."\">".$this->__($this->_buttons["submit"]["label"])."</button>"
                    ."</div></div>";
            }

            $html .= "</form>";

            return $html;
        }
    }