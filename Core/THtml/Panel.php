<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class THtml_Panel extends THtml_Base{
        private $_title   = false;
        private $_content = false;
        private $_table   = false;
        private $_footer  = false;
        private $_style   = "default";
        private $_actions = [];

        /**
         * THtml_Panel constructor.
         *
         * @param string      $style
         * @param bool|string $id
         * @param string      $class
         */
        public function __construct($style = "default", $id = false, $class = ""){
            $this->_style = $style;
            $class .= " panel panel-".$this->_style;
            parent::__construct($id, $class);
        }

        /**
         * @param string|bool $title
         *
         * @return $this
         */
        public function setTitle($title = false){
            $this->_title = $title;
            return $this;
        }

        /**
         * @param string|bool $content
         *
         * @return $this
         */
        public function setContent($content = false){
            $this->_content = $content;
            return $this;
        }

        /**
         * @param THtml_Table $table
         *
         * @return $this
         */
        public function setTable(THtml_Table $table){
            $this->_table = $table;
            return $this;
        }

        /**
         * @param string|bool $footer
         *
         * @return $this
         */
        public function setFooter($footer = false){
            $this->_footer = $footer;
            return $this;
        }

        /**
         * @param string $label
         * @param string $js
         * @param string $style
         *
         * @return $this
         */
        public function addAction($label, $js, $style = "default"){
            if($label)
                $this->_actions[] = [$label, $js, $style];
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<div".$this->_getAttributes().">";

            if($this->_title || count($this->_actions)){
                $actions = "";
                if(count($this->_actions)){
                    $actions .= '<div class="btn-group pull-right">';
                    foreach($this->_actions as list($label, $onclick, $style)){
                        $actions .= "<a href=\"javascript:void\" class=\"btn btn-$style btn-xs\" onclick=\"$onclick\">$label</a>";
                    }
                    $actions .= '</div>';
                }

                $html .= "<div class=\"panel-heading\">$this->_title $actions</div>";
            }

            if($this->_content)
                $html .= "<div class=\"panel-body\">$this->_content</div>";

            if($this->_table)
                $html .= $this->_table->get();

            if($this->_footer)
                $html .= "<div class=\"panel-footer\">$this->_footer</div>";

            $html .= "</div>";

            return $html;
        }
    }