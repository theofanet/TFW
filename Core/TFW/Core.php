<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    class TFW_Core extends TFW_Abstract{
        private static $_version    = "2.0.26";
        private static $_updaterUrl = "";

        /**
         * @var TFW_Core_Event $_events;
         */
        protected static $_events;

        /**
         * @var TFW_Cron $_runningCron
         */
        private static $_runningCron = null;


        private static $_updatesAvailable = [
            "core"   => false,
            "module" => false
        ];

        /**
         * Initialise method,
         * Load settings and initialise objects
         * used by framework
         *
         * @param bool $sessions
         * @param bool $loadCron
         *
         * @throws TFW_Exception
         */
        public static function init($sessions = true, $loadCron = false){
            set_exception_handler(array("TFW_Core", 'handlePhpExceptions'));
            set_error_handler(array("TFW_Core", 'handlePhpError'));
            register_shutdown_function(array("TFW_Core", 'handlePhpFatalError'));

            self::$_events = new TFW_Core_Event();

            if($sessions)
               TFW_Registry::initSessions();

            TFW_Registry::clearRegister();
            TFW_Registry::loadConfigs();
            TFW_Registry::loadFormData();

            self::initDatabase();
            self::initModules($loadCron);

            // TODO : Dynamic set
            TFW_Registry::register("template", TFW_Registry::getTemplate("Core"));

            if($sessions){
                $name = TFW_Registry::getConfig("project:name");

                if(TFW_Registry::getSetting("app_name"))
                    $name = TFW_Registry::getSetting("app_name");

                TFW_Registry::setVar("app_name", $name);

                // Check for core or modules updates
                $core_update   = TFW_Registry::getSetting("core.update");
                $module_update = TFW_Registry::getSetting("module.update.%");

                if($core_update)
                    self::$_updatesAvailable["core"] = true;

                if($module_update)
                    self::$_updatesAvailable["module"] = true;
            }

            self::$_events->triggerEvent(TFW_Core_Event::INIT_DONE);
        }

        /**
         * @param Exception|ParseError $exception
         */
        public static function handlePhpExceptions($exception){
            if(self::$_runningCron){
                self::$_runningCron->setAsError($exception->getMessage());
                TFW_Registry::setSetting("cron.running", 0);
                TFW_Registry::setSetting("cron.last_run", time());
            }
            else
                echo "[".$exception->getFile().":".$exception->getLine()."] ".$exception->getMessage();
        }

        /**
         * @param int    $errno
         * @param string $errstr
         * @param string $errfile
         * @param int    $errline
         *
         * @throws \TFW_Exception
         */
        protected static function throwException($errno, $errstr, $errfile = "None", $errline = -1){
            $errfile = str_replace(ROOT_PATH, '', $errfile);
            $error   = "[$errfile:$errline] $errstr";

            $exception = new TFW_Exception($error, $errno);

            if(self::$_runningCron){
                self::$_runningCron->setAsError($exception->getMessage());
                TFW_Registry::setSetting("cron.running", 0);
                TFW_Registry::setSetting("cron.last_run", time());
            }
            else
                throw $exception;
        }

        /**
         * @param int    $errno
         * @param string $errstr
         * @param string $errfile
         * @param int    $errline
         *
         * @throws \TFW_Exception
         */
        public static function handlePhpError($errno, $errstr, $errfile = "None", $errline = -1){
            if(error_reporting() & $errno)
                self::throwException($errno, $errstr, $errfile, $errline);
        }

        /**
         * @throws \TFW_Exception
         */
        public static function handlePhpFatalError() {
            $error = error_get_last();

            if($error){
                self::throwException(
                    0,
                    (isset($error['message']) ? $error['message'] : ''),
                    (isset($error['file']) ? $error['file'] : "None"),
                    (isset($error['line']) ? $error['line'] : -1)
                );
            }
        }

        /**
         * Close method
         * Called in last, close everything used by framework
         */
        public static function close(){
            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");
            if($db)
                $db->close();
        }

        /**
         * DB Initialisation
         *
         * @throws TFW_Exception
         */
        protected static function initDatabase(){
            try{
                $db = new TDB_Mysql(
                    TFW_Registry::getConfig("database:server"),
                    TFW_Registry::getConfig("database:username"),
                    TFW_Registry::getConfig("database:password"),
                    TFW_Registry::getConfig("database:database"),
                    TFW_Registry::getConfig("database:charset")
                );
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            TFW_Registry::register("db", $db);
        }

        /**
         * Used to check if there is an update available for core or modules
         * $type could be false, "core" or "module"
         *
         * @param bool|string $type
         *
         * @return bool|mixed
         */
        public static function isUpdateAvailable($type = false){
            if($type === false)
                return self::$_updatesAvailable["core"] || self::$_updatesAvailable["module"];
            else if($type == "core" || $type == "module")
                return self::$_updatesAvailable[$type];

            return false;
        }

        /**
         * Modules initialisation
         *
         * @param bool $loadCron
         * @param bool $api_mode
         *
         * @throws TFW_Exception
         */
        protected static function initModules($loadCron = true, $api_mode = false){
            $modules_dir = ROOT_PATH.DIRECTORY_SEPARATOR."Module".DIRECTORY_SEPARATOR;
            try{
                foreach(TFW_IO::getDirectories($modules_dir) as $m){
                    $module = new TFW_Module($m, $modules_dir.$m, $api_mode);
                    TFW_Registry::register("module:".$module->getKey(), $module);

                    if($loadCron){
                        foreach($module->getCronList() as $key => $freq)
                            TFW_Registry::registerCron($key, $freq);
                    }
                }

                self::$_events->triggerEvent(TFW_Core_Event::MODULES_LOADED, TFW_Registry::getRegistered("module"));
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @return string
         */
        public static function getUri(){
            $uri = "/";
            if(isset($_SERVER["REQUEST_URI"]))
                $uri = $_SERVER["REQUEST_URI"];
            if($uri != "/")
                $uri = rtrim($uri, "/");

            return $uri;
        }

        /**
         * @return Core_Model_User|null
         */
        public static function getUser(){
            $user = TFW_Registry::getRegistered("user");
            return $user;
        }

        /**
         * @param string $code
         */
        public static function setLocals($code){
            TFW_Local::clear();
            $modules = TFW_Registry::getRegistered("module");
            if($modules){
                /**
                 * @var TFW_Module $module
                 */
                foreach($modules as $module){
                    $locals_path = $module->getPath()
                        ."Local".TFW_IO::DS
                        .$code.".csv";

                    if(file_exists($locals_path)){
                        $f = new TFile_Csv($locals_path);
                        TFW_Local::add($module->getName(), $f->getData());
                        unset($f);
                    }
                }
            }
        }

        /**
         * Main loop
         *
         * @throws TFW_Exception
         */
        public static function run(){
            try{
                $uri = self::getUri();

                /*
                 * Check if login active
                 */
                $login_class    = TFW_Registry::getRegistered("template:login:controller");
                $login_fallback = TFW_Registry::getRegistered("template:login:fallback");

                if($login_class && $uri != $login_fallback){
                    /**
                     * @var TFW_Login $login
                     */
                    $login = TFW_Registry::getController($login_class);
                    if($login){
                        $user = $login->tryConnect();

                        if($user === false)
                            $uri = $login_fallback;
                        else{
                            TFW_Registry::register("user", $user);
                            self::setLocals($user->app_language);
                        }
                    }
                    else
                        throw new TFW_Exception("Login controller not found : ".$login_class);
                }

                /**
                 * @var TFW_Controller $class
                 */
                if(list(list($class_key, $action), $args) = TFW_Registry::handleRoute($uri)){
                    $class = TFW_Registry::getController($class_key);
                    if(method_exists($class, $action))
                        $class->exec($action, self::checkValues($args, false));
                    else
                        throw new TFW_Exception("Method not found - ".get_class($class)."->".$action."()");
                }
                else
                    TFW_Controller::trigger404();
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * Cron main loop
         *
         * @throws \TFW_Exception
         */
        public static function cron(){
            $isRunning = TFW_Registry::getSetting("cron.running");
            if(!$isRunning){
                TFW_Registry::setSetting("cron.running", 1);

                /**
                 * @var TFW_Cron $cron
                 */
                foreach(TFW_Registry::getCronList() as $cron){
                    self::$_runningCron = $cron;

                    try{
                        if($cron->canExecute())
                            $cron->execute();
                    }
                    catch(TFW_Exception $e){
                        $cron->setAsError($e->getMessage());
                    }
                }

                self::$_runningCron = null;
                TFW_Registry::setSetting("cron.running", 0);
                TFW_Registry::setSetting("cron.last_run", time());
            }
        }

        /**
         * @return string
         */
        public static function getTFWVersion(){
            return self::$_version;
        }

        /**
         * @return string
         */
        public static function getUpdaterUrl(){
            return self::$_updaterUrl;
        }

    }
