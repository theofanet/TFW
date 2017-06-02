<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class Core_Controller_Login extends TFW_Login{
        private $user_login;
        private $user_pass;

        private function _setCookies(){
            setcookie("username", $this->user_login, time() + 7*24*3600, null, null, false, true);
            setcookie("password", $this->user_pass, time() + 7*24*3600, null, null, false, true);
        }

        private function _checkCookies(){
            if(isset($_COOKIE["username"]) && isset($_COOKIE["password"])){
                $this->user_login = self::checkValues($_COOKIE["username"]);
                $this->user_pass  = self::checkValues($_COOKIE["password"]);
                $this->_setCookies();
            }
        }

        private function _checkSessions(){
            $username = TFW_Registry::getVar("username");
            $password = TFW_Registry::getVar("password");

            if($username)
                $this->user_login = self::checkValues($username);
            if($password)
                $this->user_pass = self::checkValues($password);
        }

        public function tryConnect(){
            $this->user_login = $this->login;
            $this->user_pass  = $this->pass;

            // CSRF Check if login form posted
            if($this->user_login && $this->user_pass){
                $token  = $this->token;
                $_token = TFW_Registry::getVar("form-csrf-token", true);
                if(!$token || $token != $_token)
                    return false;
            }

            $this->_checkSessions();
            $this->_checkCookies();

            if($this->user_login && $this->user_pass){
                $user = TFW_Registry::getModel("Core/User");
                $user
                    ->addWhere("email", $this->user_login)
                    ->addWhere("password", md5($this->user_pass))
                    ->addWhere("active", 1)
                    ->load();

                if($user->isLoaded()){
                    TFW_Registry::setVar("username", $this->user_login);
                    TFW_Registry::setVar("password", $this->user_pass);

                    $this->user_login = NULL;
                    $this->user_pass  = NULL;

                    return $user;
                }
                else{
                    $this->clearVars();
                    TFW_Flash::addErrorAlert($this->__("Unable to connect with those credentials"));
                }
            }

            return false;
        }

        public function login(){
            $project_name = TFW_Registry::getConfig("project:name");
            $project_name = ($project_name ? $project_name." - " : "");
            $this->_site_title = $project_name."Please login";

            $_token = TFW_Registry::getVar("form-csrf-token");
            if(!$_token){
                $token = base64_encode(openssl_random_pseudo_bytes(32));
                TFW_Registry::setVar("form-csrf-token", $token);
            }
            else
                $token = $_token;

            $this->render("Core/Login_Form", [
                "project_name" => $project_name,
                "token" => $token
            ], [
                'header' => 'Core/Login_Header'
            ]);
        }

        public function logout(){
            $this->clearVars();
            header("location: /");
        }

        private function clearVars(){
            TFW_Registry::getVar("username", true);
            TFW_Registry::getVar("password", true);

            if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
                unset($_COOKIE['username']);
                unset($_COOKIE['password']);

                setcookie("username");
                setcookie("password");
            }

            session_destroy();
            session_start();
        }

    }