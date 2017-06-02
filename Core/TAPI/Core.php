<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class TAPI_Core extends TFW_Core{
        private static $_method = false;
        private static $_body   = false;
        private static $_format = "application/json";
        /**
         * @var Core_Model_Api_Key
         */
        private static $_apiKey = false;

        /**
         * Handle the preflight request
         */
        private static function handlePreflights(){
            // respond to preflights
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if(isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Headers: Authorization,Api-Key,Content-Type');
                }

                exit;
            }
        }

        /**
         * @param int    $errno
         * @param string $errstr
         * @param string $errfile
         * @param int    $errline
         *
         * @throws \TAPI_Exception
         */
        protected static function throwException($errno, $errstr, $errfile = "None", $errline = -1){
            $errfile = str_replace(ROOT_PATH, '', $errfile);
            $error   = "[$errfile:$errline] $errstr";

            throw new TAPI_Exception($error, $errno);
        }

        /**
         * @param bool $sessions
         * @param bool $loadCron
         *
         * @throws \TFW_Exception
         */
        public static function init($sessions = true, $loadCron = false){
            self::handlePreflights();

            self::$_events = new TFW_Core_Event();

            // Errors handlers
            set_exception_handler(array("TFW_Core", 'handlePhpExceptions'));
            set_error_handler(array("TFW_Core", 'handlePhpError'));
            register_shutdown_function(array("TFW_Core", 'handlePhpFatalError'));

            // Configs
            TFW_Registry::clearRegister();
            TFW_Registry::loadConfigs();

            // DB and modules
            self::initDatabase();
            self::initModules(false, true);

            // Getting Method info
            self::$_method = $_SERVER['REQUEST_METHOD'];
            if(self::$_method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
                if($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE')
                    self::$_method = 'DELETE';
                else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT')
                    self::$_method = 'PUT';
                else
                    throw new TFW_Exception("Unexpected Header", 400);
            }
            self::$_method = strtoupper(self::$_method);

            /*
             * Getting Content-Type info;
             * Used to decode and encode data
             */
            if(isset($_SERVER["CONTENT_TYPE"]))
                self::$_format = $_SERVER["CONTENT_TYPE"];

            // Getting ApiKey
            if(isset($_SERVER["HTTP_API_KEY"])){
                $key = $_SERVER["HTTP_API_KEY"];
                self::$_apiKey = TFW_Registry::getModel("Core/Api_Key");
                self::$_apiKey
                    ->addWhere("active", 1)
                    ->addWhere("value", $key)
                    ->load();
            }

            if(!self::$_apiKey || !self::$_apiKey->isLoaded()) throw new TAPI_Exception("Missing or wrong API key", 400);

            if(!TAPI_Format::isAvailable(self::$_format)) throw new TAPI_Exception("Format no available : ".self::$_format, 400);

            // Trying to get the body content
            try{
                $raw = file_get_contents('php://input');
                self::$_body = TAPI_Format::decode($raw, self::$_format);
            }
            catch(TAPI_Exception $e){
                throw $e;
            }
        }

        /**
         * @return string
         */
        public static function getFormat(){
            return self::$_format;
        }

        /**
         * @return mixed|bool
         */
        public static function getBody(){
            return self::$_body;
        }

        /**
         * @return Core_Model_Api_Key
         */
        public static function getApiKey(){
            return self::$_apiKey;
        }

        /**
         * @throws \TAPI_Exception
         */
        public static function run(){
            /**
             * @var TAPI_Endpoint $endpoint
             */
            if(list(list($endpoint, $action), $args) = TFW_Registry::handleApiRoute(self::getUri(), self::$_method)){
                if(method_exists($endpoint, $action)){
                    try{
                        $result = $endpoint->exec($action, $args);
                    }
                    catch(TAPI_Exception $e){
                        throw $e;
                    }

                    TAPI_Result::return($result);
                }
                else
                    throw new TAPI_Exception("Action $action not exists in endpoint $endpoint", 400);
            }
            else
                throw new TAPI_Exception("Route correspond to nothing : [".self::$_method."] ".self::getUri());
        }

    }