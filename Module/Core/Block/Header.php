<!DOCTYPE html>
<html lang="<?= TFW_Core::getUser()->app_language ?>">
<head>
    <meta charset="<?= TFW_Registry::getConfig("project:charset") ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?= (isset($page_title) ? $page_title : "TFW Application"); ?></title>

    <?php
        $theme = "default";
        if(TFW_Registry::getSetting("application.theme"))
            $theme = TFW_Registry::getSetting("application.theme");
    ?>

    <!-- Bootstrap -->
    <?php self::addCss("Core/jquery-ui.min.css"); ?>
    <?php self::addCss("Core/font-awesome.min.css"); ?>
    <?php self::addCss("Core/bootstrap.$theme.min.css"); ?>
    <?php self::addCss("Core/toastr.min.css"); ?>
    <?php self::addCss("Core/style.css"); ?>

    <?php self::addJs("Core/jquery-3.2.1.min.js"); ?>
    <?php self::addJs("Core/jquery-ui.min.js"); ?>
    <?php self::addJs("Core/jquery.ui.touch-punch.min.js"); ?>
    <?php self::addJs("Core/bootstrap.min.js"); ?>
    <?php self::addJs("Core/toastr.min.js"); ?>
    <?php self::addJs("Core/helpers.js"); ?>
    <?php self::addJs("Core/TQuery.js"); ?>
    <?php self::addJs("Core/onload.js"); ?>

    <?php
        /*
         * Handle flashes toastr notifications
         */
        $flashes = TFW_Flash::getFlashes();
        if($flashes){
            echo "<script>"
                ."$(document).ready(function(){";
            foreach($flashes as list($content, $title, $type))
                echo "toastr.$type(\"$content\", \"$title\");";
            echo "});"
                ."</script>";
        }

        if(!isset($sideBlock))
            $sideBlock = false;
    ?>
</head>
<body>

    <!-- The overlay -->
    <div id="app-page-overlay" class="overlay">
        <!-- Overlay content -->
        <div class="overlay-content">
            <i class="fa fa-spinner fa-spin fa-3x fa-fw" aria-hidden="true"></i>
            <div id="overlay-additional-content" style="margin-top:20px;">

            </div>
        </div>
    </div>

    <?php echo $this->getBlock("Core/Navbar"); ?>

    <div class="container content-wrapper affix-content">

        <?php
            if(TFW_Core::getUser()->hasRight("Core:settings:modules") && TFW_Core::isUpdateAvailable()){
                echo "<div class=\"row\" style=\"margin:-15px 0 10px 0\">"
                    ."<div class=\"col-md-12\">";

                echo "<div class=\"bs-calltoaction bs-calltoaction-success\">"
                    ."<div class=\"row\">"
                    ."<div class=\"col-md-9 cta-contents\">"
                    ."<h1 class=\"cta-title\">".$this->__("Updates available")."</h1>"
                    ."<div class=\"cta-desc\">"
                    .(TFW_Core::isUpdateAvailable("core") ? "<p>".$this->__("Core update available to <b>%s</b>", TFW_Registry::getSetting("core.update.version"))."</p>" : "")
                    .(TFW_Core::isUpdateAvailable("module") ? "<p>".$this->__("Module(s) update available")."</p>" : "")
                    ."</div>"
                    ."</div>"
                    ."<div class=\"col-md-3 cta-button\">"
                    ."<a href=\"/settings/application/module\" class=\"btn btn-lg btn-block btn-success\">".$this->__("Update !")."</a>"
                    ."</div>"
                    ."</div>"
                    ."</div>";

                echo "</div>"
                    ."</div>";
            }
        ?>

        <div class="flashes-box">
        <?php
            /*
             * Handle flashes alerts
             */
            $flashes = TFW_Flash::getAlertsFlash();
            if($flashes){
                foreach($flashes as list($content, $title, $type)){
                    echo "<div class=\"alert alert-$type alert-dismissible\" role=\"alert\">"
                        ."<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"
                        .($title ? "<h4>$title</h4>" : "")
                        .$content
                        ."</div>";
                }
            }
        ?>
        </div>

        <?php
            if($sideBlock){
                echo "<div class=\"col-sm-3\">"
                    .$sideBlock
                    ."</div>";
            }
        ?>

        <div class="col-sm-<?= ($sideBlock ? 9 : 12) ?>">