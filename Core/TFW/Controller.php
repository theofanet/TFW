<?php

    /**
     * Created by Theo.
     */
    class TFW_Controller extends TFW_Event_Subject{
        protected $_site_title = "";
        protected $_side_block = false;
        protected $_rights = [];

        private static $_loadedJS  = [];
        private static $_loadedCSS = [];

        /*
         * Events
         */
        const EVENT_CALLED = "called";
        const EVENT_404    = "route404";
        const EVENT_RENDER = "blockRendered";

        protected $_referer = "/";


        /**
         * TFW_Controller constructor.
         */
        public function __construct(){
            $this->attach(TFW_Event_Dispatcher::getInstance());
            $this->_site_title = TFW_Registry::getVar("app_name");
        }

        /**
         * @param string $name
         *
         * @return mixed|null
         */
        public function __get($name){
            return TFW_Registry::getPostData($name);
        }

        /**
         * @param string $route
         */
        public static function goToRoute($route){
            header("location: $route");
            exit;
        }

        /**
         * Go to referer if isset. If not, go to /
         */
        public function goToReferer(){
            self::goToRoute($this->_referer);
            exit;
        }

        /**
         * @return string
         */
        public function getReferer(){
            return $this->_referer;
        }

        /**
         * Method to save referer
         */
        protected function _getReferer(){
            $this->_referer = "/";

            if(isset($_SERVER["HTTP_REFERER"])){
                $this->_referer = $_SERVER["HTTP_REFERER"];
                if(isset($_SERVER["HTTP_HOST"])){
                    $baseUrl = "http";
                    if(isset($_SERVER["REQUEST_SCHEME"]))
                        $baseUrl = $_SERVER["REQUEST_SCHEME"];
                    $baseUrl .= "://".$_SERVER["HTTP_HOST"];
                    $this->_referer = str_replace($baseUrl, "", $this->_referer);
                }
            }
        }

        /**
         * @param string $name
         * @param array  $arguments
         *
         * @return bool|mixed
         */
        public function exec($name, $arguments){
            $this->_getReferer();

            if(isset($this->_rights[$name])){
                if(!TFW_Core::getUser()->hasRight($this->_rights[$name])){
                    self::trigger404();
                    return false;
                }
            }

            return call_user_func_array([$this, $name], $arguments);
        }

        /**
         * @param string $key
         * @param array  $args
         *
         * @return $this
         * @throws TFW_Exception
         */
        public function setSideBlock($key, $args = array()){
            try{
                $this->_side_block = $this->getBlock($key, $args);
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            return $this;
        }

        /**
         * @param string $key
         * @param array  $args
         *
         * @return string
         * @throws \TFW_Exception
         */
        public function getBlock($key, $args = array()){
            $error = false;

            ob_start();
            if($old_content = ob_get_contents())
                ob_clean();

            if(count($args))
                extract($args, EXTR_PREFIX_SAME, "var");

            $filePath = false;
            try{
                $filePath = TFW_Registry::getBlockFile($key);
            }
            catch(TFW_Exception $e){
                $error = $e;
            }

            if($filePath)
                include $filePath.".php";

            if($block_content = ob_get_contents())
                ob_clean();

            echo $old_content;

            ob_end_flush();

            if($error)
                throw $error;

            return $block_content;
        }

        /**
         * Need to be called in blocks
         *
         * @param string $key
         *
         * @throws \TFW_Exception
         */
        public static function addCss($key){
            if(!isset(self::$_loadedCSS[$key])){
                $parts = explode("/", $key);
                $module = array_shift($parts);
                $file = implode(TFW_IO::DS, $parts);

                $filePath = "Module".TFW_IO::DS
                    .$module.TFW_IO::DS
                    ."Assets".TFW_IO::DS
                    ."css".TFW_IO::DS
                    .$file;

                if(file_exists(ROOT_PATH.TFW_IO::DS.$filePath)){
                    self::$_loadedCSS[$key] = true;
                    echo '<link href="/'.$filePath.'" rel="stylesheet">';
                }
                else
                    throw new TFW_Exception("Unable to find CSS file ".$key." - ".ROOT_PATH.TFW_IO::DS.$filePath);
            }
        }

        /**
         * Need to be called in blocks
         *
         * @param string $key
         *
         * @throws \TFW_Exception
         */
        public static function addJs($key){
            if(!isset(self::$_loadedJS[$key])){
                $parts = explode("/", $key);
                $module = array_shift($parts);
                $file = implode(TFW_IO::DS, $parts);

                $filePath = "Module".TFW_IO::DS
                    .$module.TFW_IO::DS
                    ."Assets".TFW_IO::DS
                    ."js".TFW_IO::DS
                    .$file;

                if(file_exists(ROOT_PATH.TFW_IO::DS.$filePath)){
                    self::$_loadedJS[$key] = true;
                    echo '<script src="/'.$filePath.'"></script>';
                }
                else
                    throw new TFW_Exception("Unable to find JS file ".$key." - ".ROOT_PATH.TFW_IO::DS.$filePath);
            }
        }

        /**
         * @param string|bool $block
         * @param array       $args
         * @param array       $template
         */
        public function render($block = false, $args = array(), $template = array()){
            self::$_loadedCSS = [];
            self::$_loadedJS  = [];

            if($old_content = ob_get_contents())
                ob_clean();

            if(isset($template["header"]))
                $header = $template["header"];
            else
                $header = TFW_Registry::getRegistered("template:header");

            if(isset($template["footer"]))
                $footer = $template["footer"];
            else
                $footer = TFW_Registry::getRegistered("template:footer");


            if($header){
                echo $this->getBlock($header, [
                    "page_title" => $this->_site_title,
                    "sideBlock"  => $this->_side_block
                ]);
            }

            if($old_content)
                echo $old_content;

            if($block)
                echo $this->getBlock($block, $args);

            if($footer)
                echo $this->getBlock($footer);

            $this->triggerEvent(self::EVENT_RENDER, [
                "controller" => $this,
                "block"      => $block,
                "args"       => $args,
                "template"   => $template
            ]);
        }

        /**
         * @throws \TFW_Exception
         */
        public static function trigger404(){
            $action404 = TFW_Registry::getRegistered("template:404");

            if($action404){
                if(list($controller, $action) = $action404){
                    try{
                        /**
                         * @var TFW_Controller $class
                         */
                        $class = TFW_Registry::getController($controller);

                        if(method_exists($class, $action)){
                            $class->triggerEvent(self::EVENT_404, [
                                "route"  => TFW_Core::getUri(),
                                "action" => $action404
                            ]);

                            $class->$action();
                        }
                        else
                            throw new TFW_Exception("404 - Method not found in controller [$controller, $action]");
                    }
                    catch(TFW_Exception $e){
                        throw $e;
                    }
                }
                else
                    throw new TFW_Exception("404 - Error in template should be [controller class, action]");
            }
            else
                throw new TFW_Exception("404 - ".TFW_Core::getUri());
        }

        /**
         * @param string $data
         */
        protected function TQuery_Error($data){
            echo json_encode(['error' => $data]);
            exit;
        }

        /**
         * @param string $data
         */
        protected function TQuery_Result($data = ''){
            echo json_encode([
                 'error'   => false,
                 'warning' => false,
                 'data'    => $data
            ]);
            exit;
        }

        /**
         * @param string     $warning
         * @param bool|mixed $data
         */
        protected function TQuery_Warning($warning, $data = false){
            echo json_encode(array(
                'error'   => false,
                'warning' => $warning,
                'data'    => $data
            ));
            exit;
        }

    }