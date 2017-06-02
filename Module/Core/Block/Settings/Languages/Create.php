<?php

    $language_code = new THtml_Input_Text("language_code");
    $language_code
        ->setPlaceholder("FR");

    $language_name = new THtml_Input_Text("language_name");
    $language_name
        ->setPlaceholder("Fran&ccedil;ais");

    // Form
    $form = new THtml_Form("/settings/language/create");
    $form
        ->addElement($this->__("Code"), $language_code)
        ->addElement($this->__("Name"), $language_name);

    // Modal Window
    $modal = new THtml_Modal("new_language_modal");
    $modal
        ->setContent($form->get())
        ->setTitle($this->__("Create a new language"))
        ->show();