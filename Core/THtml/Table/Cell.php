<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    abstract class THtml_Table_Cell extends TFW_Abstract{
        /**
         * @param string|mixed $value
         * @param array        $args
         *
         * @return string
         */
        public static function format($value, $args = []){
            return $value;
        }

        /**
         * @param string|mixed $value
         * @param array        $args
         *
         * @return string
         */
        public static function getStyle($value, $args = []){
            return "";
        }
    }