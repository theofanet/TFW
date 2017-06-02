<?php
    /**
     * Created by Theo.
     */
    if(!isset($settings))
        $settings = array();
    if(!isset($selected_group))
        $selected_group = "";
    if(!isset($selected_item))
        $selected_item = "";


    $menu = new THtml_Menu();
    foreach($settings as $group_key => $group){

        if(isset($group['label'])){

            $sub    = new THtml_Menu();
            $active = false;
            $groupActive = false;

            if(isset($group['items'])){
                foreach($group['items'] as $item_key => $item){
                    if(isset($item['label'])){
                        $active = ($selected_item == $item_key && $selected_group == $group_key);
                        if($active && !$groupActive)
                            $groupActive = true;

                        $sub->addLink($this->__($item['label']), "/settings/$group_key/$item_key", $active);
                    }
                }
            }

            $menu->addSubMenu($this->__($group["label"]), $sub, $groupActive);
        }

    }

    $menu->show();
