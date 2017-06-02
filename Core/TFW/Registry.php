<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    class TFW_Registry extends TFW_Abstract{

        const VARIABLE_NAME_REGEX = "([a-zA-Z0-9\-\_\.]+)";
        const VARIABLE_REGEX      = "[{{(.*?)}}]";

        private static $_objects  = array();
        private static $_configs  = array();
        private static $_settings = array();
        private static $_routes   = array(
            'static' => [],
            'variables' => []
        );
        private static $_controllers   = array();
        private static $_modelsClasses = array();
        private static $_templates     = array();
        private static $_helpers       = array();
        private static $_headerPlugins = array();
        private static $_form_data     = array(
            "post"  => [],
            "get"   => [],
            "files" => []
        );
        private static $_rights = array();
        private static $_menu   = array();
        private static $_menuSorted = false;
        private static $_cron = array();
        private static $_apiRoutes = array(
            'static' => [],
            'variables' => []
        );
        private static $_endpoints  = array();
        private static $_observers  = array();
        private static $_overwrites = array(
            "block"      => [],
            "model"      => [],
            "controller" => []
        );


        /**
         * Sessions init
         */
        public static function initSessions(){
            session_start();
        }

        /**
         * @param string $key
         * @param mixed  $value
         */
        public static function setVar($key, $value){
            $key = explode(":", $key);

            $item     = &$_SESSION;
            $last_key = array_pop($key);

            foreach($key as $k){
                if(!isset($item[$k]))
                    $item[$k] = array();

                $item = &$item[$k];
            }

            $item[$last_key] = $value;
        }

        /**
         * @param string $key
         * @param bool   $clear
         *
         * @return null
         */
        public static function getVar($key, $clear = false){
            $key = explode(":", $key);

            $item     = &$_SESSION;
            $last_key = array_pop($key);

            foreach($key as $k){
                if(!isset($item[$k]))
                    return null;

                $item = &$item[$k];
            }

            if(isset($item[$last_key])){
                $result = $item[$last_key];
                if($clear)
                    unset($item[$last_key]);
                return $result;
            }

            return null;
        }

        /**
         * @throws \TFW_Exception
         */
        public static function loadConfigs(){
            $conf_dir = ROOT_PATH.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR;

            try{
                foreach(TFW_IO::getFiles($conf_dir, 'php') as $cfile){
                    require_once $conf_dir.$cfile;
                }
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            if(isset($config))
                self::$_configs = $config;
        }

        /**
         * Loads GET, POST, FILES
         * Clears them
         * Secure POST and GET
         */
        public static function loadFormData(){
            self::$_form_data = array(
                "post"  => $_POST,
                "get"   => $_GET,
                "files" => $_FILES
            );

            $_POST  = [];
            $_GET   = [];
            $_FILES = [];

            array_map(array("TFW_Abstract", "checkValues"), self::$_form_data["post"]);
            array_map(array("TFW_Abstract", "checkValues"), self::$_form_data["get"]);
        }

        public static function clearRegister(){
            self::$_objects = array();
        }

        /**
         * @param string $key
         *
         * @return mixed|null
         */
        public static function getPostData($key){
            if(isset(self::$_form_data["post"][$key]))
                return self::$_form_data["post"][$key];

            return null;
        }

        /**
         * @return array
         */
        public static function getPostArray(){
            return self::$_form_data["post"];
        }

        /**
         * @param string $key
         *
         * @return mixed|null
         */
        public static function getGetData($key){
            if(isset(self::$_form_data["get"][$key]))
                return self::$_form_data["get"][$key];

            return null;
        }

        /**
         * @param string $key
         *
         * @return null|array
         */
        public static function getFilesData($key){
            if(isset(self::$_form_data["files"][$key]) && self::$_form_data["files"][$key]["name"])
                return self::$_form_data["files"][$key];

            return null;
        }

        /**
         * @return array
         */
        public static function getFilesArray(){
            return self::$_form_data["files"];
        }

        /**
         * @param string $module
         * @param array  $rights
         */
        public static function registerRights($module, $rights){
            self::$_rights[$module] = $rights;
        }

        /**
         * @param bool|string $key
         *
         * @return array|mixed
         */
        public static function getRights($key = false){
            $item = self::$_rights;

            if($key !== false){
                $key  = explode(":", $key);
                $item = &self::$_rights;
                foreach($key as $k){
                    if(!isset($item[$k]))
                        $item[$k] = array();
                    $item = &$item[$k];
                }
            }

            return $item;
        }

        /**
         * @param string $key
         * @param mixed  $obj
         */
        public static function register($key, $obj){
            $key = explode(":", $key);

            $item     = &self::$_objects;
            $last_key = array_pop($key);

            foreach($key as $k){
                if(!isset($item[$k]))
                    $item[$k] = array();

                $item = &$item[$k];
            }

            $item[$last_key] = $obj;
        }

        /**
         * @param string $key
         *
         * @return mixed|null
         */
        public static function getRegistered($key){
            $key = explode(":", $key);

            $item     = &self::$_objects;
            $last_key = array_pop($key);

            foreach($key as $k){
                if(!isset($item[$k]))
                    return null;

                $item = &$item[$k];
            }

            if(isset($item[$last_key]))
                return $item[$last_key];

            return null;
        }

        /**
         * Retrieve config who has been set in /Config/*.php files
         *
         * @param string $key
         *
         * @return string|array|null
         */
        public static function getConfig($key){
            $key = explode(":", $key);

            if(isset(self::$_configs[$key[0]])){
                if(!isset($key[1]))
                    return self::$_configs[$key[0]];
                else if(is_array(self::$_configs[$key[0]]) && isset(self::$_configs[$key[0]][$key[1]]))
                    return self::$_configs[$key[0]][$key[1]];
            }

            return null;
        }

        /**
         * Load setting from database
         *
         * @param string $key
         *
         * @return mixed|null
         * @throws \TFW_Exception
         */
        public static function getSetting($key){
            if(!isset(self::$_settings[$key])){
                /**
                 * @var TDB_Mysql $db
                 */
                $db = self::getRegistered("db");

                if($db){
                    $query = "SELECT * FROM app_settings WHERE setting_key LIKE \"$key\" LIMIT 0, 1";
                    try{
                        $qrez   = $db->execQuery($query);
                        $result = $db->fetchResults($qrez, true, true);

                        if($result)
                            self::$_settings[$key] = $result->setting_value;
                        else
                            return null;
                    }
                    catch(TFW_Exception $e){
                        throw $e;
                    }
                }
                else
                    throw new TFW_Exception("getSetting - Database not initialised");
            }

            return self::$_settings[$key];
        }

        /**
         * @param string      $key
         * @param string|bool $value
         *
         * @throws \TFW_Exception
         */
        public static function setSetting($key, $value){
            /**
             * @var TDB_Mysql $db
             */
            $db = self::getRegistered("db");

            if($db){
                $exits = self::getSetting($key);
                try{
                    if($exits === NULL)
                        $query = "INSERT INTO app_settings (setting_key, setting_value) VALUES(\"$key\", \"$value\")";
                    else
                        $query = "UPDATE app_settings SET setting_value=\"$value\" WHERE setting_key LIKE \"$key\"";

                    $db->getResults($query);
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                throw new TFW_Exception("setSetting - Database not initialised");
        }

        /**
         * @param string $key
         *
         * @throws \TFW_Exception
         */
        public static function removeSetting($key){
            /**
             * @var TDB_Mysql $db
             */
            $db = self::getRegistered("db");

            if($db){
                try{
                    $query = "DELETE FROM app_settings WHERE setting_key LIKE \"$key\"";
                    $db->execQuery($query);
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                throw new TFW_Exception("removeSetting - Database not initialised");
        }

        /**
         * @return array
         */
        public static function getRoutes(){
            return self::$_routes;
        }

        /**
         * Add route to routes list
         * Gives the route key and the [controller key, action]
         *
         * @param string $key
         * @param array  $handler
         *
         * @throws \TFW_Exception
         */
        public static function registerRoute($key, $handler){
            if(!is_array($handler) || count($handler) < 2)
                throw new TFW_Exception("Route error with handler - ".$key);

            $matches = array();
            if(preg_match_all(self::VARIABLE_REGEX, $key, $matches)){
                if(isset(self::$_routes["variables"][$key]))
                    throw new TFW_Exception("Variable route already exists - ".$key);

                $regex = "@^".str_replace($matches[0], self::VARIABLE_NAME_REGEX, $key)."$@D";
                self::$_routes["variables"][$key] = [$regex, $handler, $matches[1]];
            }
            else{
                if(isset(self::$_routes["static"][$key]))
                    throw new TFW_Exception("Static route already exists - ".$key);

                self::$_routes["static"][$key] = $handler;
            }
        }

        /**
         * @param string $route
         *
         * @return array|bool
         * @throws TFW_Exception
         */
        public static function handleRoute($route){
            if(isset(self::$_routes["static"][$route]))
                return [self::$_routes["static"][$route], []];
            else{
                foreach(self::$_routes["variables"] as $key => $r){
                    list($regex, $handler, $param) = $r;

                    if(preg_match($regex, $route, $matches)){
                        array_shift($matches);

                        if(count($matches) == count($param))
                            return [$handler, $matches];
                        else
                            throw new TFW_Exception("Variable route parameters mismatches - ".$route);
                    }
                }
            }

            return null;
        }

        public static function getApiRoutes(){
            return self::$_apiRoutes;
        }

        /**
         * @param string $key
         * @param array  $data
         *
         * @throws \TFW_Exception
         */
        public static function registerApiRoute($key, $data){
            foreach($data as $method => list($endpoint_key, $action)){
                $method = strtoupper($method);

                if(!isset(self::$_endpoints[$endpoint_key])){
                    $parts = explode("/", $endpoint_key);
                    $class = $parts[0]."_Api_";
                    if(count($parts) == 1)
                        $class .= "Base";
                    else
                        $class .= $parts[1];

                    if(!class_exists($class)) throw new TFW_Exception("API route ".$key." - Endpoint class not found : ".$class);

                    self::$_endpoints[$endpoint_key] = new $class();
                }

                $endpoint = self::$_endpoints[$endpoint_key];

                $matches = array();
                if(preg_match_all(self::VARIABLE_REGEX, $key, $matches)){
                    if(!isset(self::$_apiRoutes["variables"][$method]))
                        self::$_apiRoutes["variables"][$method] = [];

                    if(isset(self::$_apiRoutes["variables"][$method][$key]))
                        throw new TFW_Exception("API variable route already exists for method $method - $key");

                    $regex = "@^".str_replace($matches[0], self::VARIABLE_NAME_REGEX, $key)."$@D";
                    self::$_apiRoutes["variables"][$method][$key] = [$regex, $endpoint, $action, $matches[1]];
                }
                else{
                    if(!isset(self::$_apiRoutes["static"][$key]))
                        self::$_apiRoutes["static"][$key] = [];

                    if(isset(self::$_apiRoutes["static"][$key][$method]))
                        throw new TFW_Exception("API static route already exists for method $method - ".$key);

                    self::$_apiRoutes["static"][$key][$method] = [$endpoint, $action];
                }
            }
        }

        /**
         * @param string $route
         * @param string $method
         *
         * @return array|bool
         * @throws TFW_Exception
         */
        public static function handleApiRoute($route, $method){
            if(isset(self::$_apiRoutes["static"][$route][$method]))
                return [self::$_apiRoutes["static"][$route][$method], []];
            else{
                foreach(self::$_apiRoutes["variables"][$method] as $key => $r){
                    list($regex, $endpoint, $action, $param) = $r;

                    if(preg_match($regex, $route, $matches)){
                        array_shift($matches);

                        if(count($matches) == count($param))
                            return [[$endpoint, $action], $matches];
                        else
                            throw new TFW_Exception("API variable route parameters mismatches - ".$route);
                    }
                }
            }

            return null;
        }

        /**
         * @param string $name
         * @param array  $data
         */
        public static function addTemplate($name, $data){
            self::$_templates[$name] = $data;
        }

        /**
         * @param string $key
         *
         * @return bool|array
         */
        public static function getTemplate($key){
            if(isset(self::$_templates[$key]))
                return self::$_templates[$key];

            return false;
        }

        /**
         * @param string $key
         *
         * @return TFW_Controller
         * @throws \TFW_Exception
         */
        public static function getController($key){
            $controller_class = null;

            if(!isset(self::$_controllers[$key])){
                $subKey = $key;
                // Handle overwrite
                if(isset(self::$_overwrites["controller"][$key]))
                    $subKey = self::$_overwrites["controller"][$key];

                $parts = explode("/", $subKey);

                $controller_class = $parts[0]."_Controller_";
                if(count($parts) == 1)
                    $controller_class .= "Base";
                else
                    $controller_class .= $parts[1];

                if(!class_exists($controller_class)) throw new TFW_Exception("Unable to find controller ".$key);

                self::$_controllers[$key] = new $controller_class();
            }

            return self::$_controllers[$key];
        }

        /**
         * @param string $key
         *
         * @return null|string
         * @throws \TFW_Exception
         */
        private static function _getModelClass($key){
            $model_class = null;

            if(!isset(self::$_modelsClasses[$key])){
                $subKey = $key;
                // Handle overwrite
                if(isset(self::$_overwrites["model"][$key]))
                    $subKey = self::$_overwrites["model"][$key];

                $parts  = explode("/", $subKey);

                $model_class = $parts[0]."_Model_";
                if(count($parts) == 1)
                    $model_class .= "Base";
                else
                    $model_class .= $parts[1];

                if(!class_exists($model_class)) throw new TFW_Exception("Unable to find model ".$key);

                self::$_modelsClasses[$key] = $model_class;
            }
            else
                $model_class = self::$_modelsClasses[$key];

            return $model_class;
        }

        /**
         * @param string $key
         *
         * @return TDB_Model
         * @throws \TFW_Exception
         */
        public static function getModel($key){
            try{
                $model_class = self::_getModelClass($key);
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            if($model_class)
                return new $model_class();
            else
                throw new TFW_Exception("Unable to find model ".$key);
        }

        /**
         * @param string $key
         *
         * @return TFW_Helper
         * @throws \TFW_Exception
         */
        public static function getHelper($key){
            try{
                if(!isset(self::$_helpers[$key])) {
                    $parts = explode('/', $key);

                    $class = $parts[0].'_Helper_';
                    if(count($parts) == 2)
                        $class .= $parts[1];
                    else
                        $class .= 'Base';

                    if(!class_exists($class)) throw new TFW_Exception("Unable to find helper $key");

                    self::$_helpers[$key] = new $class();
                }

                return self::$_helpers[$key];
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param array  $menu
         * @param string $module
         *
         * @return mixed
         */
        private static function _checkMenuItem($menu, $module = "Core"){
            if(isset($menu["items"]) && count($menu["items"])){
                $base_menu_item = [
                    "title" => "",
                    "module" => $module,
                    "position" => 100000,
                    "current" => [],
                    "items" => [],
                    "route" => false
                ];

                foreach($menu["items"] as $i => $item){
                    $menu["items"][$i] = array_replace_recursive($base_menu_item, $item);

                    if(isset($item["items"]) && count($item["items"]))
                        $menu["items"][$i] = self::_checkMenuItem($menu["items"][$i], $module);
                }
            }

            return $menu;
        }


        /**/

        /**
         * @param array  $menu
         * @param string $module
         */
        public static function registerMenu($menu, $module = "Core"){
            $_menu = &self::$_menu;
            $base_menu_item = [
                "title" => "",
                "module" => $module,
                "position" => 100000,
                "current" => [],
                "items" => [],
                "route" => false
            ];

            foreach($menu as $key => $data){
                if(isset($_menu[$key]))
                    $_menu[$key] = array_replace_recursive($_menu[$key], $data);
                else
                    $_menu[$key] = array_replace_recursive($base_menu_item, $data);

                $_menu[$key] = self::_checkMenuItem($_menu[$key], $module);
            }
        }

        /**
         * @param array $menu
         *
         * @return array
         */
        private static function _sortMenuItem($menu){
            uasort($menu, self::_sortMenuItemBy('position'));

            foreach($menu as $key => $data){
                if(isset($data["items"]))
                    $menu[$key]["items"] = self::_sortMenuItem($data["items"]);
            }

            return $menu;
        }

        /**
         * @param string $key
         *
         * @return \Closure
         */
        private static function _sortMenuItemBy($key){
            return function($a, $b) use($key){
                if(!isset($a[$key]))
                    $a[$key] = 1000000;
                if(!isset($b[$key]))
                    $b[$key] = 1000000;

                if($a[$key] == $b[$key])
                    return 0;

                return ($a[$key] < $b[$key]) ? -1 : 1;
            };
        }

        /**
         * @return array
         */
        public static function getMenu(){
            if(!self::$_menuSorted){
                uasort(self::$_menu, self::_sortMenuItemBy('position'));
                foreach(self::$_menu as $key => $data){
                    if(isset($data["items"])){
                        self::$_menu[$key]["items"] = self::_sortMenuItem($data["items"]);
                    }
                }
            }

            return self::$_menu;
        }

        /**
         * @param string $block
         * @param int $position
         */
        public static function addHeaderPlugin($block, $position = 0){
            while(isset(self::$_headerPlugins[$position]))
                $position++;
            self::$_headerPlugins[$position] = $block;
        }

        /**
         * @return array
         */
        public static function getHeaderPlugins(){
            ksort(self::$_headerPlugins);
            return self::$_headerPlugins;
        }

        /**
         * @param string $key
         * @param string $frequency
         *
         * @throws \TFW_Exception
         */
        public static function registerCron($key, $frequency){
            if(!isset(self::$_cron[$key])){
                $parts = explode(":", $key);
                if(count($parts) != 2) throw new TFW_Exception("Wrong CRON key format : $key, should be like this : Module/Class:action");
                $action = $parts[1];

                $parts = explode("/", $parts[0]);
                $class = $parts[0]."_Cron_";
                if(count($parts) == 1)
                    $class .= "Base";
                else
                    $class .= $parts[1];
                if(!class_exists($class)) throw new TFW_Exception("Unable to find CRON ".$key);

                self::$_cron[$key] = new $class($key, $action, $frequency);
            }
            else
                throw new TFW_Exception("CRON key already used : $key");
        }

        /**
         * @return array
         */
        public static function getCronList(){
            return self::$_cron;
        }

        /**
         * @param string $key
         * @param string $subject
         *
         * @throws \TFW_Exception
         */
        public static function addObserver($key, $subject){
            try{
                if(!isset(self::$_observers[$key])) {
                    $parts = explode('/', $key);

                    $class = $parts[0].'_Observer_';
                    if(count($parts) == 2)
                        $class .= $parts[1];
                    else
                        $class .= 'Base';

                    if(!class_exists($class)) throw new TFW_Exception("Unable to find observer $key");

                    self::$_observers[$key] = $class;
                }

                TFW_Event_Dispatcher::addObserver(self::$_observers[$key], $subject);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param string $key_origin
         * @param string $key_final
         * @param string $type
         *
         * @throws \TFW_Exception
         */
        public static function overwrite($key_origin, $key_final, $type){
            if(!isset(self::$_overwrites[$type])) throw new TFW_Exception("Unable to make overwrite type $type");

            $rec = true;
            while($rec){
                if(isset(self::$_overwrites[$type][$key_origin]))
                    $key_origin = self::$_overwrites[$type][$key_origin];
                else
                    $rec = false;
            }

            self::$_overwrites[$type][$key_origin] = $key_final;
        }

        /**
         * @param string $key
         *
         * @return string
         * @throws \TFW_Exception
         */
        public static function getBlockFile($key){
            if(isset(self::$_overwrites["block"][$key]))
                $key = self::$_overwrites["block"][$key];

            $parts = explode('/', $key);
            if(count($parts) != 2)
                throw new TFW_Exception("Block key error - ".$key);

            $filePath = ROOT_PATH.TFW_IO::DS
                ."Module".TFW_IO::DS
                .str_replace('_', TFW_IO::DS, $parts[0]).TFW_IO::DS
                ."Block".TFW_IO::DS
                .str_replace('_', TFW_IO::DS, $parts[1]);

            if(!file_exists($filePath.".php"))
                throw new TFW_Exception('Block '.$key.' not found in '.$filePath.'.php');

            return $filePath;
        }
    }