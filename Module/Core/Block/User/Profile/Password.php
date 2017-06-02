<?php
    /**
     * @var Core_Model_User $user
     */
    $user = TFW_Core::getUser();

    if($user){
        $password = new THtml_Input_Password("password");
        $password
            ->setPlaceholder($this->__("Password"));

        $confirm = new THtml_Input_Password("password_confirm");
        $confirm
            ->setPlaceholder($this->__("Confirm password"));



        $form = new THtml_Form("/user/profile/password/update");
        $form
            ->addElement($this->__("Password"), $password)
            ->addElement($this->__("Confirm"), $confirm)
            ->show();
    }
