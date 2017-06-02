<?php
    /**
     * Created by Theo.
     */

    if(!isset($group))
        $group = 'app_settings';

    if(!isset($item))
        $item = 'environment';

    if(!isset($settingsData))
        $settingsData = array();

    if($group && $item){
        if(isset($settingsData[$group]['items'][$item])){
            $settingsItem = $settingsData[$group]['items'][$item];

            if(isset($settingsItem['block']))
                echo $this->getBlock($settingsItem['block']);
            else{
                $form = new THtml_Form("/settings/save");

                $settings_container = new THtml_Panel();
                $settings_container
                    ->setTitle($this->__($settingsItem['label']));

                if(isset($settingsItem['fields'])){
                    foreach($settingsItem['fields'] as $config_key => $field){
                        if(isset($field['type']) && isset($field['label'])){
                            $element = null;
                            switch($field['type']){
                                case 'text':
                                    $element = new THtml_Input_Text('configs_var['.$config_key.']');
                                    $element
                                        ->setPlaceholder($this->__($field['label']))
                                        ->setValue(TFW_Registry::getSetting($config_key));
                                    break;
                                case 'select':
                                    $element = new THtml_Input_Select('configs_var['.$config_key.']');
                                    $element
                                        ->setSelected(TFW_Registry::getSetting($config_key));

                                    if(isset($field['data'])){
                                        foreach($field['data'] as $value => $label){
                                            if(is_array($label)){
                                                $trad = call_user_func_array(array($this, '__'), $label);
                                                $element->add($value, $trad);
                                            }
                                            else
                                                $element->add($value, $this->__($label));
                                        }
                                    }
                                    else if(isset($field['model']) && isset($field['key']) && isset($field['value'])){
                                        $list = TFW_Registry::getModel($field['list']);
                                        $data = $list->getList(false, true);
                                        foreach($data as $d)
                                            $element->add($d[$field['key']], $d[$field['value']]);
                                    }

                                    break;
                                case 'textarea':
                                    $element = new THtml_Input_TextArea('configs_var['.$config_key.']');
                                    $element
                                        ->addAttribute('style', 'min-height:200px;')
                                        ->setPlaceholder($this->__($field['label']))
                                        ->setValue(TFW_Registry::getSetting($config_key));

                                    break;
                            }

                            if($element){
                                if(!TFW_Core::getUser()->hasRight("Core:settings:update"))
                                    $element->setReadonly();

                                $form
                                    ->addElement(
                                        $this->__($field['label']),
                                        $element,
                                        false
                                    );
                            }
                        }
                    }
                }

                if(!TFW_Core::getUser()->hasRight("Core:settings:update"))
                    $form->setActive(false);

                $form
                    ->addHidden("setting_group", $group)
                    ->addHidden("setting_item", $item);

                $settings_container
                    ->setContent($form->get())
                    ->show();
            }

        }
    }
