<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $this->_site_title; ?></title>

    <?php
        $theme = "default";
        if(TFW_Registry::getSetting("application.theme"))
            $theme = TFW_Registry::getSetting("application.theme");
    ?>

    <!-- Bootstrap -->
    <?php $this->addCss("Core/bootstrap.$theme.min.css"); ?>
    <?php self::addCss("Core/toastr.min.css"); ?>
    <?php $this->addCss("Core/login.css"); ?>

    <?php $this->addJs("Core/jquery-3.2.1.min.js"); ?>
    <?php $this->addJs("Core/bootstrap.min.js"); ?>
    <?php self::addJs("Core/toastr.min.js"); ?>

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
    ?>
</head>
<body>

<div class="container">