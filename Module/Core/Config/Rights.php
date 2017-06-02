<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */

    $config["rights"] = [

        "profile" => [
            "label" => "User profile",
            "items" => [
                "info" => "Update information",
                "pass" => "Change password"
            ]
        ],

        "users" => [
            "label" => "Users & groups",
            "items" => [
                "groups:see"    => "See groups list",
                "groups:create" => "Create a new group",
                "groups:update" => "Update a existing group",
                "users:see"     => "See users list",
                "users:create"  => "Create a new user",
                "users:update"  => "Update a existing user"
            ]
        ],

        "settings" => [
            "label" => "Settings",
            "items" => [
                "see"     => "See settings",
                "update"  => "Update settings",
                "modules" => "Can update modules or core",
                "api"     => [
                    "label" => "API",
                    "items" => [
                        "create" => "Create new API keys",
                        "update" => "Update existing API keys"
                    ]
                ],
                "cron"  => [
                    "label" => "CRON",
                    "items" => [
                        "reset" => "Re-lunch CRON if stopped"
                    ]
                ]
            ]
        ],

        "logs" => [
            "label" => "Logs",
            "items" => [
                "see"      => "See log",
                "delete"   => "Remove log",
                "download" => "Download log"
            ]
        ]

    ];