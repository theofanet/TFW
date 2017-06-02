<?php

    /**
     * Created by Theo.
     */
    class THtml_List extends THtml_Base{
        protected $_elements = [];

        /**
         * THtml_List constructor.
         *
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($id = false, $class = ""){
            $class .= " list-group";
            parent::__construct($id, $class);
        }

        /**
         * @param string $content
         * @param array  $attributes
         *
         * @return $this
         */
        public function add($content, $attributes = []){
            $c = "";
            if(isset($attributes["class"]))
                $c = $attributes["class"];
            $attributes["class"] = "list-group-item $c";
            $this->_elements[] = [$content, $attributes];
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<ul ".$this->_getAttributes().">";
            foreach($this->_elements as list($content, $attributes)){
                $attr = "";
                foreach($attributes as  $k => $v)
                    $attr .= " $k=\"$v\"";
                $html .= "<li$attr>$content</li>";
            }
            $html .= "</ul>";

            return $html;
        }
    }