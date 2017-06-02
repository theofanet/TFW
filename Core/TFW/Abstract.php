<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    abstract class TFW_Abstract{
        protected $_last_error = false;

        /**
         * @param mixed $data
         */
        public static function dump($data){
            echo '<div style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:10000;color:#fff;background:rgba(0, 0, 0, 0.8);padding:30px;overflow: scroll;">'
                .'<div id="var_dump_container">'
                .'<pre>';

            print_r($data);

            echo '</pre>'
                .'</div></div>';

            exit;
        }

        /**
         * @param string|array $value
         * @param bool         $check_empty
         * @param bool         $html_entities
         *
         * @return array|bool|string
         */
        public static function checkValues($value, $check_empty = true, $html_entities = true){
            if($value === false || ($value != 0 && $check_empty && empty($value)))
                return false;

            if(is_array($value)){
                $result = array();
                foreach($value as $id => $v)
                    $result[$id] = self::checkValues($v, $check_empty, $html_entities);
                return $result;
            }
            else{
                $value = trim($value);
                $value = addslashes($value);
                $value = htmlentities((string)$value, ENT_COMPAT, "UTF-8");

                if($html_entities)
                    $value = strip_tags($value);

                $value = nl2br($value);
            }

            return $value;
        }

        /**
         * @return bool|string
         */
        public function getLastError(){
            return $this->_last_error;
        }


        /**
         * Needed to permit __ and ___ called in static
         *
         * @param string $name
         * @param array  $arguments
         *
         * @return mixed
         */
        public static function __callStatic($name, $arguments){
            if($name == "__" || $name == "___")
                $name = "s$name";

            return call_user_func_array([get_called_class(), $name], $arguments);
        }

        /**
         * Translation function
         * Params should be : String Text, ... Args
         *
         * @return string
         * @throws TFW_Exception
         */
        public function __(){
            try{
                $args = func_get_args();
                $text = array_shift($args);

                $parts = explode("_", get_class($this));
                $namespace = array_shift($parts);

                return stripslashes(TFW_Local::translate($text, $args, $namespace));
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }


        /**
         * Translation function
         * Params should be : String Text, ... Args
         *
         * @return string
         * @throws TFW_Exception
         */
        public static function s__(){
            try{
                $args = func_get_args();
                $text = array_shift($args);

                $parts = explode("_", get_called_class());
                $namespace = array_shift($parts);

                return stripslashes(TFW_Local::translate($text, $args, $namespace));
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * Translation function
         * Params should be : String Text, ... Args, String Namespace
         *
         * @return string
         * @throws TFW_Exception
         */
        public function ___(){
            try{
                $args = func_get_args();
                $text = array_shift($args);
                $namespace = array_pop($args);

                return stripslashes(TFW_Local::translate($text, $args, $namespace));
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * Translation function
         * Params should be : String Text, ... Args, String Namespace
         *
         * @return string
         * @throws TFW_Exception
         */
        public static function s___(){
            try{
                $args = func_get_args();
                $text = array_shift($args);
                $namespace = array_pop($args);

                return stripslashes(TFW_Local::translate($text, $args, $namespace));
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

    }