<?php
    $logout_url = TFW_Registry::getRegistered("template:login:logout");

    /**
     * @var Core_Helper_Time $timeHelper
     */
    $timeHelper         = TFW_Registry::getHelper("Core/Time");
    $notifications      = TFW_Core::getUser()->getNotifications(true);
    $notifications_list = "";
    $new_notifications  = 0;

    foreach($notifications as $not){
        if(!$not->seen)
            $new_notifications++;

        $notifications_list .= $this->getBlock("Core/Navbar_User_Notification", [
            "notification" => $not
        ]);
    }
?>
<li class="dropdown">
    <a href="#" id="user-notification" class="<?= ($new_notifications ? "notif-animation" : "") ?> dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell" aria-hidden="true"></i>
    </a>
    <input type="hidden" name="nb-new-notification" value="<?= $new_notifications ?>" />
    <ul class="dropdown-menu mega-dropdown" style="width:300px;">
        <?= $notifications_list ? $notifications_list : "<li style=\"text-align:center\">".$this->__("None")."</li>" ?>
    </ul>
</li>

<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <?php
            $user = TFW_Core::getUser();
            if($user)
                echo "$user->first_name $user->last_name";
        ?>
        <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
        <?php if(TFW_Core::getUser()->hasRight("Core:profile")){ ?>
        <li><a href="/user/profile"><span class="glyphicon glyphicon-user"></span> <?= $this->__("Profile"); ?></a></li>
        <?php } if(TFW_Core::getUser()->hasRight("Core:settings:see")){ ?>
        <li><a href="/settings"><span class="glyphicon glyphicon-cog"></span> <?= $this->__("Settings"); ?></a></li>
        <?php } if(TFW_Core::getUser()->hasRight("Core:users")) { ?>
        <li><a href="/users"><span class="fa fa-users"></span> <?= $this->__("Users & groups"); ?></a></li>
        <?php } if(TFW_Core::getUser()->hasRight("Core:logs")) { ?>
        <li><a href="/logs"><span class="fa fa-file-text-o"></span> <?= $this->__("Logs"); ?></a></li>
        <?php } ?>
        <li class="divider"></li>
        <li><a href="<?php echo $logout_url; ?>"><span class="glyphicon glyphicon-off"></span> <?= $this->__("Logout"); ?></a></li>
    </ul>
</li>