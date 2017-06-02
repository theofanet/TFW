<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class Core_Controller_User_Profile extends TFW_Controller{
        protected $_rights = [
            "index" => "Core:profile",
            "save"  => "Core:profile:info",
            "updatePass" => "Core:profile:pass"
        ];

        public function index(){
            $this->render("Core/User_Profile");
        }

        public function save(){
            $firstName = $this->first_name;
            $lastName  = $this->last_name;
            $email     = $this->email;
            $language  = $this->language;

            if($firstName || $lastName || $email || $language){
                $user = TFW_Core::getUser();

                if($user){
                    $user->first_name   = $firstName;
                    $user->last_name    = $lastName;
                    $user->email        = $email;
                    $user->app_language = $language;

                    if($user->save())
                        TFW_Flash::addSuccess($this->__("Profile updated"));
                    else
                        TFW_Flash::addError($this->__("Unable to update profile. %s", $this->__($user->getLastError())));
                }
                else
                    TFW_Flash::addError($this->__("Unable to load user"));
            }
            else
                TFW_Flash::addWarningAlert($this->__("Nothing to update for profile"));

            self::goToRoute("/user/profile");
        }

        public function updatePass(){
            $password = $this->password;
            $confirm  = $this->password_confirm;

            if($password && $confirm){
                if($confirm == $password){
                    $user = TFW_Core::getUser();

                    $user->password = md5($password);

                    if($user->save())
                        TFW_Flash::addSuccess($this->__("Password updated"));
                    else
                        TFW_Flash::addError($this->__("Unable to update profile. %s", $this->__($user->getLastError())));
                }
                else
                    TFW_Flash::addError($this->__("Passwords are not the same"));
            }
            else
                TFW_Flash::addError($this->__("Missing data"));

            self::goToRoute("/user/profile");
        }
    }