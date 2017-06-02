<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_Helper_Menu extends TFW_Helper{

        public function get(){
            $menu = TFW_Registry::getMenu();

            $html = '<ul class="nav navbar-nav">';
            $html .= $this->_getMenuContent($menu);
            $html .= '</ul>';

            return $html;
        }

        private function _getMenuContent($menu, $lvl = 0){
            $html = "";

            foreach($menu as $key => $data){
                $usr_data       = false;
                $usr_data_value = 1;

                if(isset($data['user_data']))
                    $usr_data = $data['user_data'];
                if(isset($data['user_data_value']))
                    $usr_data_value = $data['user_data_value'];

                if($usr_data === false || TFW_Core::getUser()->$usr_data == $usr_data_value){
                    if(!isset($data['rights']) || TFW_Core::getUser()->hasRight($data['rights'])){
                        $page_key = false;
                        if(isset($data['route']))
                            $page_key = $data['route'];

                        $current = false;
                        if($page_key == TFW_Core::getUri())
                            $current = true;
                        else if(isset($data['current']) && in_array(TFW_Core::getUri(), $data['current']))
                            $current = true;

                        $hasChildren = isset($data['items']) && count($data['items']);

                        $html .= '<li class="'.($current ? ' active' : '').($hasChildren && $lvl > 0 ? ' dropdown-submenu' : '').'">'
                            .'<a href="'.($page_key ? $page_key : '#').'"';

                        if($hasChildren)
                            $html .= ' class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"';

                        $html .= '>'
                            .$this->___($data['title'], $data["module"])
                            .'</a>';

                        if($hasChildren){
                            $html .= '<ul class="dropdown-menu">';
                            $html .= $this->_getMenuContent($data['items'], $lvl + 1);
                            $html .= '</ul>';
                        }

                        $html .= '</li>';
                    }
                }
            }

            return $html;
        }

    }