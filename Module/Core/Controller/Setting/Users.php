<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 21/02/2017
     */
    class Core_Controller_Setting_Users extends TFW_Controller{
        protected $_rights = [
            "index"         => "Core:users",
            "saveUser"      => "Core:users:users",
            "getUserModal"  => "Core:users:users:update",
            "saveGroup"     => "Core:users:groups",
            "getGroupModal" => "Core:users:groups:update"
        ];

        /**
         * Index page for users and groups admin
         */
        public function index(){
            $this->render("Core/Settings_Users");
        }

        /**
         * Method to retrieve the modal for user update
         *
         * @param int $id_user
         */
        public function getUserModal($id_user){
            if(!$id_user)
                $this->TQuery_Error($this->__("Missing user ID"));

            $user = TFW_Registry::getModel("Core/User");
            $user->load($id_user);

            if($user->isLoaded())
                $this->TQuery_Result($this->getBlock("Core/Settings_Users_Edit", ["user" => $user]));
            else
                $this->TQuery_Error($this->__("Unable to load user %s", $id_user));
        }

        /**
         * Method to create or update an user
         *
         *
         * @param bool|int $id_user
         */
        public function saveUser($id_user = false){
            if((!$id_user && !TFW_Core::getUser()->hasRight("Core:users:users:create"))
                || ($id_user && !TFW_Core::getUser()->hasRight("Core:users:users:update"))){
                self::trigger404();
                exit;
            }

            $first_name = $this->first_name;
            $last_name  = $this->last_name;
            $email      = $this->email;
            $id_group   = $this->group;
            $active     = $this->active_account;

            if($first_name && $last_name && $email && $id_group){
                $user = TFW_Registry::getModel("Core/User");
                if($id_user)
                    $user->load($id_user);
                else{
                    $user
                        ->addWhere("email", $email)
                        ->load();

                    // User already exists
                    if($user->isLoaded()){
                        TFW_Flash::addError($this->__("User with the same email already exists"));
                        self::goToRoute("/users");
                    }
                }

                $user->first_name = $first_name;
                $user->last_name  = $last_name;
                $user->email      = $email;
                $user->id_group   = $id_group;
                $user->active     = $active ? 1 : 0;

                if(!$user->isLoaded()){
                    $pass = TFW_Registry::getHelper("Core/Text")->generatePass(5, 5);
                    $user->password = md5($pass);
                    TFW_Flash::addWarning($pass);

                    // Mail
                    $content = $this->__("Hello %s,", "$first_name $last_name")."<br /><br />"
                        .$this->__("Please find attached your credentials to connect to %s", TFW_Registry::getConfig("project:name"))
                        ."<br /><br />"
                        .$this->__("Username : %s", $email)."<br />"
                        .$this->__("Password : %s", $pass)
                        ."<br /><br />"
                        .$this->__("Thank you");

                    $mail = new TFW_Mail();
                    $mail
                        ->addTo($email)
                        ->setSubject($this->__("Credentials for %s", TFW_Registry::getConfig("project:name")))
                        ->setTemplate("Core/Mail.html", [
                            "[%%%%%BODY%%%%%]"       => $content,
                            "[%%%%%FOOTER%%%%%]"     => "&copy; Ekotek 2017-2018",
                            "[%%%%%APP_TITLE%%%%%]"  => TFW_Registry::getConfig("project:name"),
                            "[%%%%%MY_ADDRESS%%%%%]" => "",
                            "[%%%%%LOGO_URL%%%%%]"   => TFW_Registry::getSetting("app_logo")
                        ]);

                    if(!$mail->send())
                        TFW_Flash::addWarning($pass);
                }

                if($user->save())
                    TFW_Flash::addSuccess($this->__("User ".($id_user ? "updated" : "created")));
                else
                    TFW_Flash::addError($this->__("Unable to save user : %s", $this->__($user->getLastError())));
            }
            else
                TFW_Flash::addError($this->__("Missing data"));

            self::goToRoute("/users");
        }

        /**
         * Method to create or update a user group
         *
         * @param bool|int $id_group
         */
        public function saveGroup($id_group = false){
            if((!$id_group && !TFW_Core::getUser()->hasRight("Core:users:groups:create"))
                || ($id_group && !TFW_Core::getUser()->hasRight("Core:users:groups:update"))){
                self::trigger404();
                exit;
            }

            $name   = $this->name;
            $rights = $this->rights;

            if($name){
                $group = TFW_Registry::getModel("Core/User_Group");

                if($id_group)
                    $group->load($id_group);
                else{
                    $group
                        ->addWhere("name", $name)
                        ->load();

                    // group already exists
                    if($group->isLoaded()){
                        TFW_Flash::addError($this->__("Group with the same name already exists"));
                        self::goToRoute("/users");
                    }
                }

                if(!is_array($rights))
                    $rights = [];

                $group->name   = $name;
                $group->rights = base64_encode(json_encode(array_values($rights)));

                if($group->save())
                    TFW_Flash::addSuccess($this->__("Group ".($id_group ? "updated" : "created")));
                else
                    TFW_Flash::addError($this->__("Unable to save group : %s", $this->__($group->getLastError())));
            }

            self::goToRoute("/users");
        }

        /**
         * Method to retrieve the users group update modal
         *
         * @param int $id_group
         */
        public function getGroupModal($id_group){
            if(!$id_group)
                $this->TQuery_Error($this->__("Missing group ID"));

            $group = TFW_Registry::getModel("Core/User_Group");
            $group->load($id_group);

            if($group->isLoaded())
                $this->TQuery_Result($this->getBlock("Core/Settings_Users_Groups_Edit", ["group" => $group]));
            else
                $this->TQuery_Error($this->__("Unable to load group %s", $id_group));
        }

    }