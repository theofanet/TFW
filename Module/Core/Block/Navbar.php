<?php
    $navbarmode = "default";
    if(TFW_Registry::getSetting("navbar.mode"))
        $navbarmode = TFW_Registry::getSetting("navbar.mode");
?>

<nav class="navbar navbar-<?= $navbarmode ?> navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <?php
                if(TFW_Registry::getSetting("app_logo"))
                    echo '<a href="/" class="navbar-brand"><img src="'.TFW_Registry::getSetting("app_logo").'" style="height:35px;margin:-7px 0;" /></a>';
            ?>

            <a class="navbar-brand" href="/">
                <?= TFW_Registry::getVar("app_name") ?>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <ul class="nav navbar-nav navbar-right" id="right-menu-items">
                <?php
                    foreach(TFW_Registry::getHeaderPlugins() as $plugin)
                        echo "<li class=\"dropdown\">".$this->getBlock($plugin)."</li>";

                    $user = TFW_Core::getUser();
                    if($user)
                        echo $this->getBlock("Core/Navbar_User");
                ?>
            </ul>

            <?= TFW_Registry::getHelper("Core/Menu")->get(); ?>

        </div>


    </div>
</nav>
