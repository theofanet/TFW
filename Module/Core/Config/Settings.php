<?php
    /**
     * Created by Theo.
     */

    $config["settings"] = [
        'application' => [
            'label'    => 'Application settings',
            'position' => 0,
            'items'    => [
                'environment' => [
                    'label'    => 'Environment',
                    'position' => 0,
                    'fields'   => [
                        'app_name' => [
                            'type'  => 'text',
                            'label' => 'Application name'
                        ],

                        'app_logo' => [
                            'type'  => 'text',
                            'label' => 'Application logo'
                        ],

                        'app_copyright' => [
                            'type'  => 'text',
                            'label' => 'Copyright'
                        ],

                        'application.theme' => [
                            'type'  => "select",
                            "label" => "Theme",
                            "data"  => [
                                "default"   => "Default",
                                "cosmo"     => "Cosmo",
                                "flatly"    => "Flatly",
                                "lumen"     => "Lumen",
                                "paper"     => "Paper",
                                "sandstone" => "Sandstone",
                                "simplex"   => "Simplex",
                                "black"     => "Black"
                            ]
                        ],

                        "navbar.mode" => [
                            "type"  => "select",
                            "label" => "Menu colors",
                            "data"  => [
                                "default" => "Normal",
                                "inverse" => "Inverse"
                            ]
                        ],

                        'debug' => [
                            'type'  => 'select',
                            'label' => 'Debug mode',
                            'data'  => [
                                0 => 'No',
                                1 => 'Yes'
                            ]
                        ],

                        'dev_env' => [
                            'type'  => 'select',
                            'label' => 'Environment',
                            'data'  => [
                                'dev'  => 'Development',
                                'prod' => 'Production'
                            ]
                        ]
                    ]
                ],

                'tables' => [
                    'label'    => 'Tables',
                    'position' => 1,
                    'fields'   => [
                        'tables.filters.text.mode' => [
                            'type'  => 'select',
                            'label' => 'Text filter mode',
                            'data'  => [
                                1 => 'Expert',
                                2 => 'Simple'
                            ]
                        ]
                    ]
                ],

                'module' => [
                    'label'    => 'Modules',
                    'position' => 1,
                    'block'    => 'Core/Settings_Module'
                ],

                'cron' => [
                    'label'    => 'Cron',
                    'position' => 2,
                    'block'    => 'Core/Settings_Cron'
                ],

                'api' => [
                    'label'    => 'API Keys',
                    'position' => 3,
                    'block'    => 'Core/Settings_Api'
                ],

                'lang' => [
                    'label' => 'Application languages',
                    'block' => 'Core/Settings_Languages'
                ]
            ]
        ]
    ];

