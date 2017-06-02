<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */

    /**
     * @var Core_Model_User_Group $group
     */
    $name = new THtml_Input_Text("name");
    $name
        ->setPlaceholder($this->__("Group name"));


    /**
     * @param THtml_Tree $tree
     * @param array      $rights
     * @param string     $_value
     * @param string     $_path
     * @param string     $module
     *
     * @return mixed
     */
    function constructModuleRightsTree($tree, $rights, $_value, $_path, $module = "Core"){
        foreach($rights as $key => $data){
            $lbl  = $key;
            $val  = false;
            if($_value)
                $val = "$_value:$key";
            if(isset($data["label"]))
                $lbl = $data["label"];
            else if(is_string($data))
                $lbl = $data;

            $lbl = TFW_Abstract::s___($lbl, $module);

            $tree->addElement($lbl, $lbl, (isset($data["items"]) ? false : $val), $_path);

            if(isset($data["items"]))
                $tree = constructModuleRightsTree($tree, $data["items"], $val, "$_path:$lbl", $module);
        }

        return $tree;
    }

    $tree = new THtml_Tree("rights");
    foreach(TFW_Registry::getRights() as $module => $rights){
        $tree->addElement($module, TFW_Abstract::s___($module, $module));
        $tree = constructModuleRightsTree($tree, $rights, $module, $module, $module);
    }

    /*
     * Form and modals data
     */
    $modal_id       = "group_modal";
    $modal_title    = $this->__("Create user group");
    $form_action    = "/users/group/save";
    $modal_autoOpen = false;
    $validate_lbl   = "Create";

    /**
     * @var Core_Model_User_Group $group
     */
    if(isset($group) && $group->isLoaded()){
        $name->setValue($group->name);
        $tree->setSelected($group->rights);

        $form_action   .= "/".$group->getId();
        $modal_id      .= "_".$group->getId();
        $modal_title    = $this->__("Update user group %s", $group->name);
        $modal_autoOpen = true;
        $validate_lbl   = "Update";
    }


    // Form
    $form = new THtml_Form($form_action);
    $form
        ->showActions(false)
        ->addElement($this->__("Group name"), $name)
        ->addElement($this->__("Rights"), $tree);


    // Modal
    $modal = new THtml_Modal($modal_id);
    $modal
        ->setAutoOpen($modal_autoOpen)
        ->setTitle($modal_title)
        ->setContent($form->get())
        ->addAction($this->__($validate_lbl), '$(\'#'.$form->getId().'\').submit();', "success")
        ->addAction($this->__("Cancel"), 'Modal.toggle(\''.$modal->getId().'\');', "danger")
        ->show();
