<?php

    /**
     * Created by Theo.
     */
    class THtml_Tree extends THtml_Base{
        private $_elements = [];
        private $_selected = false;

        /**
         * THtml_Tree constructor.
         *
         * @param string      $name
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($name, $id = false, $class = ""){
            parent::__construct($id, $class);
            $this->addAttribute('name', $name);
        }

        /**
         * @return bool|string
         */
        public function getName(){
            return $this->getAttribute("name");
        }

        /**
         * @param string      $key
         * @param string      $label
         * @param string|bool $value
         * @param string|bool $path
         *
         * @return $this
         */
        public function addElement($key, $label, $value = false, $path = false){
            $toInsert = &$this->_elements;

            if($path !== false){
                $parts = explode(":", $path);
                foreach($parts as $k){
                    if(isset($toInsert[$k]['children']))
                        $toInsert = &$toInsert[$k]['children'];
                }
            }

            $toInsert[$key] = [
                "label"    => $label,
                "value"    => $value,
                "children" => []
            ];

            return $this;
        }

        /**
         * @param array|string $selected
         *
         * @return $this
         */
        public function setSelected($selected){
            $this->_selected = $selected;
            return $this;
        }

        /**
         * @param array $element
         * @param bool  $selectElements
         *
         * @return string
         */
        private function _getRecursiveList($element, $selectElements = false){
            $html = "<li".((isset($element["value"]) && $element["value"]) ? " data-value=\"".$element["value"]."\"" : "").">"
                .$element["label"];

            if($selectElements && $element["value"])
                $this->_selected[] = $element["value"];

            if(isset($element["children"]) && count($element["children"])){
                $html .= "<ul>";
                foreach($element["children"] as $el)
                    $html .= $this->_getRecursiveList($el, $selectElements);
                $html .= "</ul>";
            }

            $html .= "</li>";

            return $html;
        }

        /**
         * @return string
         */
        public function get(){
            //Adding JS component
            TFW_Controller::addJs("Core/treeview.min.js");

            $html = "<div".$this->_getAttributes().">"
                ."<ul>";

            $selectAll = false;
            if($this->_selected == "*"){
                $selectAll = true;
                $this->_selected = [];
            }

            foreach($this->_elements as $el)
                $html .= $this->_getRecursiveList($el, $selectAll);

            $html .= "</ul>"
                ."</div>";

            $selected = "[]";
            if($this->_selected){
                if(is_array($this->_selected))
                    $selected = json_encode($this->_selected);
                else
                    $selected = $this->_selected;
            }

            $html .= "<script>"
                ."$('#$this->_id').treeview({"
                ."data: $selected"
                ."});"
                ."</script>";

            return $html;
        }
    }