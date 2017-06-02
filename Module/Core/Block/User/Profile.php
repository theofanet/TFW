<?php
    /**
     * @var Core_Model_User $user
     */
    $user = TFW_Core::getUser();
    if($user){
        echo "<div class=\"page-header\">"
            ."<h1>".$this->__('User profile')." <small>$user->first_name $user->last_name</small></h1>"
            ."</div>";

        $info_panel = new THtml_Panel();
        $info_panel
            ->setTitle($this->__("Information"))
            ->setContent($this->getBlock("Core/User_Profile_Information"));


        $pass_panel = new THtml_Panel();
        $pass_panel
            ->setTitle($this->__("Password"))
            ->setContent($this->getBlock("Core/User_Profile_Password"))
            ->setFooter($this->__("If you need to reset your password"));

        $rights = [
            "info" => $user->hasRight("Core:profile:info"),
            "pass" => $user->hasRight("Core:profile:pass")
        ];

        $grid = new THtml_Grid();

        if($rights["info"])
            $grid->addColumn($info_panel, $rights["pass"] ? 7 : 12);
        if($rights["pass"])
            $grid->addColumn($pass_panel, $rights["info"] ? 5 : 12);

        $grid
            ->addCurrentRow()
            ->show();
    }
