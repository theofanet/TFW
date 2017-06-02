<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    class TFW_Local extends TFW_Abstract{

        private static $_data;

        const DEFAULT_NAMESPACE = "Core";

        /**
         * @param string $namespace
         * @param array  $data
         */
        public static function add($namespace, $data){
            foreach($data as $d){
                if(count($d) == 2)
                    self::$_data[$namespace][$d[0]] = addslashes($d[1]);
            }
        }

        /**
         * Clears locals
         */
        public static function clear(){
            self::$_data = [];
        }

        /**
         * @param string      $text
         * @param array       $args
         * @param bool|string $namespace
         *
         * @return string
         */
        public static function translate($text, $args, $namespace = false){
            if($namespace === false)
                $namespace = self::DEFAULT_NAMESPACE;

            if(isset(self::$_data[$namespace][$text]))
                return @vsprintf(self::$_data[$namespace][$text], $args);
            else if($namespace != self::DEFAULT_NAMESPACE && isset(self::$_data[self::DEFAULT_NAMESPACE][$text]))
                return @vsprintf(self::$_data[self::DEFAULT_NAMESPACE][$text], $args);

            return @vsprintf($text, $args);
        }

    }