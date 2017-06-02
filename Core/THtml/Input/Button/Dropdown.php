<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */
    class THtml_Input_Button_Dropdown extends THtml_Input_Button{
        const SEPARATOR_KEY = "==SEPARATOR==";
        const HEADER_KEY    = "==HEADER==";

        protected $_elements  = [];
        protected $_split     = false;

        protected $_containerClass = "";


        /**
         * THtml_Input_Button_Dropdown constructor.
         *
         * @param string $style
         * @param bool   $id
         * @param string $class
         */
        public function __construct($style = "default", $id = false, $class = ""){
            $this->_containerClass = $class;
            $class .= " dropdown-toggle";
            parent::__construct($style, $id, $class);
            $this->addAttributes([
                "data-toggle"   => "dropdown",
                "aria-haspopup" => "true",
                "aria-expanded" => "false"
            ]);
        }

        /**
         * @param string $label
         * @param string $action
         * @param bool   $enable
         *
         * @return $this
         */
        public function addElement($label, $action, $enable = true){
            $this->_elements[] = [$label, $action, $enable];
            return $this;
        }

        /**
         * @param string $label
         *
         * @return $this
         */
        public function addHeader($label){
            $this->_elements[] = [self::HEADER_KEY, $label, false];
            return $this;
        }

        /**
         * @return $this
         */
        public function addSeparator(){
            $this->_elements[] = [self::SEPARATOR_KEY, false, false];
            return $this;
        }

        /**
         * @param bool $split
         *
         * @return $this
         */
        public function setSplit($split = true){
            $this->_split = $split;
            return $this;
        }

        /**
         * @param string $class
         *
         * @return $this
         */
        public function addClass($class){
            $classes = explode(" ", $class);
            foreach($classes as $c){
                if(!isset($this->_classes[$c]))
                    $this->_classes[$c] = $c;
                if($c != "dropdown-toggle" && strstr($c, "btn") === false)
                    $this->_containerClass .= " $c";
            }
            return $this;
        }

        /**
         * @return int
         */
        public function count(){
            return count($this->_elements);
        }

        /**
         * @return string
         */
        public function get(){
            $value = $this->getAttribute("value");
            $this->removeAttribute("value");

            $html = "<div class=\"btn-group $this->_containerClass\">"
                .($this->_split ? "<button type=\"button\" class=\"btn btn-$this->_style $this->_containerClass\">$value</button>" : "")
                ."<button ".$this->_getAttributes().">"
                .($this->_split ? "" : $value)." <span class=\"caret\"></span>"
                ."</button>"
                ."<ul class=\"dropdown-menu\">";

            foreach($this->_elements as list($label, $js, $enable)){
                if($label == self::SEPARATOR_KEY)
                    $html .= "<li role=\"separator\" class=\"divider\"></li>";
                else if($label == self::HEADER_KEY)
                    $html .= "<li class=\"dropdown-header\">$js</li>";
                else
                    $html .= "<li class=\"".(!$enable ? "disable" : "")."\"><a href=\"javascript:void\" onclick=\"$js\">$label</a></li>";
            }

            $html .= "</ul>"
                ."</div>";

            return $html;
        }
    }