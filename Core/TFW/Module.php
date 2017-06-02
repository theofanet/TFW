<?php

    /**
     * TFrameWork2
     *
     * User: theo
     * Date: 09/02/2017
     */
    class TFW_Module extends TFW_Abstract{
        private $_name    = null;
        private $_key     = null;

        private $_version = 0.0;
        private $_active  = true;

        private $_path = null;

        private $_configs = [];

        /**
         * TFW_Module constructor.
         * Load modules settings
         *
         * @param string $name
         * @param string $dir
         * @param bool   $api_mode
         *
         * @throws TFW_Exception
         */
        public function __construct($name, $dir, $api_mode = false){
            $this->_name = $name;

            $config = [];
            trim($dir, TFW_IO::DS);
            $this->_path = $dir.TFW_IO::DS;
            $dir = $this->_path."Config".TFW_IO::DS;

            foreach(TFW_IO::getFiles($dir) as $file)
                include_once $dir.$file;

            $this->_configs = $config;

            if(!isset($this->_configs["module"])
                || !isset($this->_configs["module"]["key"]))
                throw new TFW_Exception($this->_name." - Missing module key");

            $this->_key     = $this->_configs["module"]["key"];
            if(isset($this->_configs["module"]["version"]))
                $this->_version = $this->_configs["module"]["version"];
            if(isset($this->_configs["module"]["active"]))
               $this->_active  = $this->_configs["module"]["active"];

            if($this->_active){
                // Load every thing needed in normal mode
                if(!$api_mode){
                    /*
                     * Loading module's routes
                     */
                    if(isset($this->_configs["routes"])){
                        foreach($this->_configs["routes"] as $route => $data)
                            TFW_Registry::registerRoute($route, $data);
                    }

                    /*
                     * Adding settings
                     */
                    if(isset($this->_configs["settings"]))
                        TFW_Registry::register("settings:$this->_name", $this->_configs["settings"]);

                    /*
                     * Adding template if one
                     */
                    if(isset($this->_configs["template"])){
                        /*
                         * Header plugins if any
                         */
                        if(isset($this->_configs["template"]["plugins"]["header"])){
                            foreach($this->_configs["template"]["plugins"]["header"] as $position => $p)
                                TFW_Registry::addHeaderPlugin($p, $position);
                        }

                        TFW_Registry::addTemplate($this->_name, $this->_configs["template"]);
                    }

                    /*
                     * Register menu if present
                     */
                    if(isset($this->_configs["menu"]))
                        TFW_Registry::registerMenu($this->_configs["menu"], $this->_name);
                }
                else{
                    // Load every thing needed for API Mode

                    /*
                     * Loading module's API routes
                     */
                    if(isset($this->_configs["api"])){
                        foreach($this->_configs["api"] as $route => $data)
                            TFW_Registry::registerApiRoute($route, $data);
                    }
                }

                // Do the things needed for API and not API mode

                /*
                 * Register rights if presents
                 */
                if(isset($this->_configs["rights"]))
                    TFW_Registry::registerRights($this->_name, $this->_configs["rights"]);

                /*
                 * Looking for observers
                 */
                if(isset($this->_configs["observers"])){
                    foreach($this->_configs["observers"] as $subject => $observer)
                        TFW_Registry::addObserver($observer, $subject);
                }

                /*
                 * Looking for overwrites
                 */
                if(isset($this->_configs["overwrites"])){
                    foreach($this->_configs["overwrites"] as $type => $list){
                        foreach($list as $origin => $final){
                            try{
                                TFW_Registry::overwrite($origin, $final, $type);
                            }
                            catch(TFW_Exception $e){
                                throw $e;
                            }
                        }
                    }
                }

                /*
                 * Looking for DB updates
                 */
                try{
                    $this->_checkUpdates();
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
        }

        /**
         * @return string
         */
        public function getKey(){
            return $this->_key;
        }

        /**
         * @return string
         */
        public function getName(){
            return $this->_name;
        }

        /**
         * @return string
         */
        public function getPath(){
            return $this->_path;
        }

        /**
         * @return float
         */
        public function getVersion(){
            return $this->_version;
        }

        /**
         * @return array
         */
        public function getCronList(){
            if(isset($this->_configs["cron"]))
                return $this->_configs["cron"];

            return [];
        }

        /**
         * @return bool
         */
        public function isActive(){
            return $this->_active;
        }

        /**
         * @param string $key
         *
         * @return null|string|array
         */
        public function getConfig($key){
            $key = explode(":", $key);

            if(isset($this->_configs[$key[0]])){
                if(!isset($key[1]))
                    return $this->_configs[$key[0]];
                else if(is_array($this->_configs[$key[0]]) && isset($this->_configs[$key[0]][$key[1]]))
                    return $this->_configs[$key[0]][$key[1]];
            }

            return null;
        }

        /**
         * @throws TFW_Exception
         */
        private function _checkUpdates(){
            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");

            if($db){
                try{
                    $qrez = $db->execQuery("SELECT * FROM app_modules WHERE module_key LIKE '".$this->_key."'");
                }
                catch(TFW_Exception $e){
                    throw $e;
                }

                $module = $db->fetchResults($qrez, true, true);
                if($module){
                    if(version_compare($this->_version, $module->version)){
                        try{
                            $this->_update($module->version + 0.1, $this->_version);
                        }
                        catch(TFW_Exception $e){
                            throw $e;
                        }

                        try{
                            $db->execQuery("UPDATE app_modules SET version='".$this->_version."', timestamp=".time()." WHERE module_key LIKE '".$this->_key."'");
                        }
                        catch(TFW_Exception $e){
                            throw $e;
                        }
                    }
                }
                else{
                    try{
                        $this->_install();
                    }
                    catch(TFW_Exception $e){
                        throw $e;
                    }
                }
            }
            else
                throw new TFW_Exception($this->_name." - CheckUpdates - Database not initialised");
        }

        /**
         * @throws TFW_Exception
         */
        private function _install(){
            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");
            if($db){
                try{
                    $this->_update(0.1, $this->_version);
                }
                catch(TFW_Exception $e){
                    throw $e;
                }

                try{
                    $db->execQuery("INSERT INTO app_modules (module_key, version, timestamp) VALUES('".$this->_key."', '".$this->_version."', ".time().")");
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                throw new TFW_Exception($this->_name." - Install - Database not updated");
        }

        /**
         * @param $from
         * @param bool $to
         * @throws TFW_Exception
         */
        private function _update($from, $to = false){
            if($to === false)
                $to = $this->_version;

            $from = explode(".", $from);
            if(count($from) >= 2)
                $from = $from[0].".".$from[1];
            else
                $from = $from[0];

            $to = explode(".", $to);
            if(count($to) >= 2)
                $to = $to[0].".".$to[1];
            else
                $to = $to[0];

            $v = floatval($from);
            while($v <= floatval($to)){
                $class_name = $this->_name."_Update_".str_replace(".", "", $v);

                if(class_exists($class_name)){
                    try{
                        /**
                         * @var TDB_Update $class
                         */
                        $class = new $class_name();
                        $class->run();
                        TDB_Updater_Core::lunch();
                        $class->populate();
                    }
                    catch(TFW_Exception $e){
                        throw $e;
                    }
                }

                $v += 0.1;
            }
        }
    }