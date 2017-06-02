<?php
    if(!isset($project_name))
        $project_name = "";

    if(!isset($token))
        $token = "";
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-lock"></span>
        <?php echo $project_name; ?>
        Login
    </div>
    <div class="panel-body">

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

        <form class="form-horizontal" role="form" action="" method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>" />
            <div class="form-group">
                <label for="inputLogin" class="col-sm-3 control-label">
                    Login
                </label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="inputLogin" placeholder="Login code or email" name="login" required>
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword" class="col-sm-3 control-label">
                    Password
                </label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" id="inputPassword" placeholder="Password" name="pass" autocomplete="off" required>
                </div>
            </div>
            <div class="form-group last">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-success btn-sm">
                        Sign in
                    </button>
                    <button type="reset" class="btn btn-default btn-sm">
                        Reset
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-footer">
        Forget password ? <a href="#">Help me</a>
    </div>
</div>
