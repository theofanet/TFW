<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */

    $config["routes"] = [
        // Login
        "/login" => ["Core/Login", "login"],
        "/logout" => ["Core/Login", "logout"],

        // Connected user profile
        "/user/profile" => ["Core/User_Profile", "index"],
        "/user/profile/save" => ["Core/User_Profile", "save"],
        "/user/profile/password/update" => ["Core/User_Profile", "updatePass"],
        "/notification/seen" => ["Core", "setNotificationSeen"],

        // settings
        "/settings" => ["Core/Setting", "index"],
        "/settings/{{group}}/{{item}}" => ["Core/Setting", "index"],
        "/settings/save" => ["Core/Setting", "save"],
        "/settings/language/create" => ["Core/Setting", "newLanguage"],
        "/settings/api/save" => ["Core/Setting_Api", "saveApiKey"],
        "/settings/api/edit/{{id_key}}" => ["Core/Setting_Api", "getKeyModal"],
        "/settings/api/save/{{id_key}}" => ["Core/Setting_Api", "saveApiKey"],
        "/settings/cron/restart" => ["Core/Setting", "restartCron"],
        "/settings/cron/lunch" => ["Core/Setting", "lunchCron"],
        "/settings/module/update" => ["Core/Setting_Module", "update"],
        "/settings/core/update" => ["Core/Setting_Module", "updateCore"],

        // Logs
        "/logs" => ["Core/Setting_Log", "index"],
        "/logs/{{log_file}}" => ["Core/Setting_Log", "index"],
        "/logs/{{log_file}}/delete" => ["Core/Setting_Log", "delete"],
        "/logs/{{log_file}}/download" => ["Core/Setting_Log", "download"],

        // Users and groups
        "/users" => ["Core/Setting_Users", "index"],
        "/users/save" => ["Core/Setting_Users", "saveUser"],
        "/users/save/{{id_user}}" => ["Core/Setting_Users", "saveUser"],
        "/users/edit/{{id_user}}" => ["Core/Setting_Users", "getUserModal"],
        "/users/group/save" => ["Core/Setting_Users", "saveGroup"],
        "/users/group/save/{{id_group}}" => ["Core/Setting_Users", "saveGroup"],
        "/users/group/edit/{{id_group}}" => ["Core/Setting_Users", "getGroupModal"],
    ];