<?php

    /**
     * Created by Theo.
     */
    class THtml_Modal extends THtml_Base{
        private $_title    = false;
        private $_content  = "";
        private $_actions  = [];
        private $_fade     = true;
        private $_autoOpen = false;
        private $_size     = "";

        /**
         * THtml_Modal constructor.
         *
         * @param bool   $id
         * @param string $class
         */
        public function __construct($id = false, $class = ""){
            $class .= " modal fade";
            parent::__construct($id, $class);
        }

        /**
         * @param string $title
         *
         * @return $this
         */
        public function setTitle($title){
            $this->_title = $title;
            return $this;
        }

        /**
         * @param string $content
         *
         * @return $this
         */
        public function setContent($content){
            $this->_content = $content;
            return $this;
        }

        /**
         * @param string $title
         * @param string $js
         * @param string $class
         *
         * @return $this
         */
        public function addAction($title, $js, $class = "default"){
            $this->_actions[] = [$title, $js, $class];
            return $this;
        }

        /**
         * @param bool $fade
         *
         * @return $this
         * @throws TFW_Exception
         */
        public function setFade($fade = true){
            if($fade != $this->_fade){
                $this->_fade = $fade;
                if($this->_fade)
                    $this->addClass("fade");
                else
                    $this->removeClass("fade");
            }

            return $this;
        }

        /**
         * @param bool $auto
         *
         * @return $this
         */
        public function setAutoOpen($auto = true){
            $this->_autoOpen = $auto;
            return $this;
        }

        /**
         * @return $this
         */
        public function setLarge(){
            $this->_size = "modal-lg";
            return $this;
        }

        /**
         * @return $this
         */
        public function setSmall(){
            $this->_size = "modal-sm";
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            $html = "<div tabindex=\"-1\" role=\"dialog\" ".$this->_getAttributes().">"
                ."<div class=\"modal-dialog $this->_size\" role=\"document\">"
                ."<div class=\"modal-content\">";

            /*
             * Header
             */
            if($this->_title){
                $html .= "<div class=\"modal-header\">"
                    ."<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"
                    ."<h4 class=\"modal-title\">$this->_title</h4>"
                    ."</div>";
            }

            /*
             * Content
             */
            $html .= "<div class=\"modal-body\">"
                .$this->_content
                ."</div>";

            /*
             * Footer
             */
            if(count($this->_actions)){
                $html .= "<div class=\"modal-footer\">";
                foreach($this->_actions as list($title, $js, $class))
                    $html .= "<button type=\"button\" class=\"btn btn-$class\" onclick=\"$js\">$title</button>";
                $html .= "</div>";
            }

            $html .= "</div>"
                ."</div>"
                ."</div>";

            if($this->_autoOpen)
                $html .= '<script>Modal.toggle(\''.$this->_id.'\');</script>';

            return $html;
        }
    }