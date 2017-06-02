<?php
    /**
     * Created by Theo.
     */

    $config["template"] = [
        "header" => "Core/Header",
        "footer" => "Core/Footer",

        "404"    => ["Core", "render404"],

        "login" => [
            "fallback" => "/login",
            "controller" => "Core/Login",
            "logout" => "/logout"
        ],

        /*"plugins" => [
            "header" => [
                "Core/Header_Plugin"
            ]
        ]*/
    ];