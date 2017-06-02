<?php

    /**
     * Created by Theo.
     */
    class THtml_Menu extends THtml_Base{

        protected $_elements = array();

        /**
         * THtml_Menu constructor.
         *
         * @param bool   $id
         * @param string $class
         */
        public function __construct($id = false, $class = ""){
            $class .= " list-group";
            parent::__construct($id, $class);
        }

        /**
         * @param string $title
         * @param string $link
         * @param bool   $active
         * @param bool   $disabled
         *
         * @return $this
         */
        public function addLink($title, $link, $active = false, $disabled = false){
            $this->_elements[] = array(
                'type'     => 'link',
                'title'    => $title,
                'url'      => $link,
                'active'   => $active,
                'disabled' => $disabled
            );

            return $this;
        }

        /**
         * @param string      $title
         * @param THtml_Menu  $subMenu
         * @param bool        $active
         * @param bool        $disabled
         *
         * @return $this
         */
        public function addSubMenu($title, THtml_Menu $subMenu, $active = false, $disabled = false){
            $this->_elements[] = array(
                'type'     => 'sub_menu',
                'title'    => $title,
                'sub'      => $subMenu,
                'active'   => $active,
                'disabled' => $disabled
            );

            return $this;
        }

        public function getAsSubMenu($title){
            $html = '<div class="list-group-item">'
                .'<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void" role="button" aria-haspopup="true" aria-expanded="false">'
                .$title.' <span class="caret"></span>'
                .'</a>'
                .'<ul class="dropdown-menu">';

            /**
             * @var THtml_Menu $elem['sub']
             */
            foreach($this->_elements as $elem){
                if($elem['type'] == 'link')
                    $html .= '<li role="presentation" class="'.($elem['active'] ? 'active' : '').($elem['disabled'] ? ' disabled' : '').'"><a href="'.$elem['url'].'">'.$elem['title'].'</a></li>';
                else if($elem['type'] == 'sub_menu')
                    $html .= $elem['sub']->getAsSubMenu($elem['title']);
            }

            $html .= '</ul>'
                .'</div>';


            return $html;
        }

        /**
         * @return string
         */
        public function get(){
            $html = '<div'.$this->_getAttributes().' role="group">';

            foreach($this->_elements as $elem){
                if($elem['type'] == 'link') {
                    $html .= '<a class="list-group-item'
                        .($elem['active'] ? ' active' : '')
                        .($elem['disabled'] ? ' disabled' : '')
                        .'" href="'.$elem['url'].'">'
                        .$elem['title']
                        .'</a></li>';
                }
                else if($elem['type'] == 'sub_menu')
                    $html .= $elem['sub']->getAsSubMenu($elem['title']);
            }

            $html .= '</div>';


            return $html;
        }
    }