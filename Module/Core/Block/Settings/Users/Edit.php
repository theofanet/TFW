<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */
    $first_name = new THtml_Input_Text("first_name");
    $first_name
        ->setPlaceholder($this->__("First name"));

    $last_name = new THtml_Input_Text("last_name");
    $last_name
        ->setPlaceholder($this->__("Last name"));

    $email = new THtml_Input_Text("email");
    $email
        ->setPlaceholder($this->__("Email"));

    $group = new THtml_Input_Select("group");
    foreach(TFW_Registry::getModel("Core/User_Group")->getList(false) as $g)
        $group->add($g->id, $g->name);

    $active = new THtml_Input_Checkbox("active_account");
    $active
        ->setLabel($this->__("Active account"));


    // populate inputs and update form data if user loaded
    $form_url       = "/users/save";
    $modal_title    = $this->__("Create a new user");
    $modal_id       = "user_modal";
    $validate_lbl   = "Create";
    $modal_autoOpen = false;

    /**
     * @var Core_Model_User $user
     */
    if(isset($user) && $user->isLoaded()){
        $first_name->setValue($user->first_name);
        $last_name->setValue($user->last_name);
        $email->setValue($user->email);
        $group->setSelected($user->id_group);
        $active->setChecked($user->active);

        $form_url      .= "/".$user->getId();
        $modal_id      .= "_".$user->getId();
        $modal_title    = $this->__("Update user %s %s", $user->first_name, $user->last_name);
        $validate_lbl   = "Update";
        $modal_autoOpen = true;
    }

    // Form
    $form = new THtml_Form($form_url);
    $form
        ->showActions(false)
        ->addElement($this->__("First name"), $first_name)
        ->addElement($this->__("Last name"), $last_name)
        ->addElement($this->__("Email"), $email)
        ->addElement($this->__("Group"), $group)
        ->addElement("", $active);

    // Modal window
    $modal = new THtml_Modal($modal_id);
    $modal
        ->setAutoOpen($modal_autoOpen)
        ->setTitle($modal_title)
        ->setContent($form->get())
        ->addAction($this->__($validate_lbl), '$(\'#'.$form->getId().'\').submit();', "success")
        ->addAction($this->__("Cancel"), 'Modal.toggle(\''.$modal->getId().'\');', "danger")
        ->show();