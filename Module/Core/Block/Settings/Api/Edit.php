<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */

    $label = new THtml_Input_Text("key_label");
    $label
        ->setPlaceholder($this->__("API key label"));

    $group = new THtml_Input_Select("key_group");
    foreach(TFW_Registry::getModel("Core/User_Group")->getList(false) as $g)
        $group->add($g->id, $g->name);

    $active = new THtml_Input_Checkbox("key_active");
    $active
        ->setLabel($this->__("Key active"))
        ->setChecked();

    $route       = "/settings/api/save";
    $modal_title = "Create new API key";
    $auto_open   = false;
    $modal_id    = "edit_api_key";
    /**
     * @var Core_Model_Api_Key $api_key
     */
    if(isset($api_key) && $api_key->isLoaded()){
        $route      .= "/".$api_key->getId();
        $modal_title = "Edit API key";
        $auto_open   = true;
        $modal_id   .= "_".$api_key->getId();
        $label->setValue($api_key->label);
        $group->setSelected($api_key->id_group);
        $active->setChecked($api_key->active);
    }

    $form = new THtml_Form($route);
    $form
        ->addElement($this->__("Label"), $label)
        ->addElement($this->__("Rights group"), $group)
        ->addElement(false, $active, false)
        ->showActions(false);

    $modal = new THtml_Modal($modal_id);
    $modal
        ->setTitle($this->__($modal_title))
        ->setContent($form->get())
        ->setAutoOpen($auto_open)
        ->addAction($this->__("Save"), '$(\'#'.$form->getId().'\').submit();', "success")
        ->addAction($this->__("Cancel"), 'Modal.toggle(\''.$modal->getId().'\');', "danger")
        ->show();