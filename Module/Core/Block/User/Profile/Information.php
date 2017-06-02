<?php
    /**
     * @var Core_Model_User $user
     */
    $user = TFW_Core::getUser();

    if($user){
        $first_name = new THtml_Input_Text("first_name");
        $first_name
            ->setPlaceholder($this->__("First name"))
            ->setValue($user->first_name);

        $last_name = new THtml_Input_Text("last_name");
        $last_name
            ->setPlaceholder($this->__("Last name"))
            ->setValue($user->last_name);

        $email = new THtml_Input_Text("email");
        $email
            ->setPlaceholder($this->__("Email"))
            ->setValue($user->email);

        $language = new THtml_Input_Select("language");
        $langs = TFW_Registry::getModel("Core/Language");
        foreach($langs->getList() as $l)
            $language->add($l->lang_code, $l->lang_name);
        $language->setSelected($user->app_language);


        $form = new THtml_Form("/user/profile/save");
        $form
            ->addElement($this->__("First name"), $first_name)
            ->addElement($this->__("Last name"), $last_name)
            ->addElement($this->__("Email"), $email)
            ->addElement($this->__("Language"), $language)
            ->show();
    }
