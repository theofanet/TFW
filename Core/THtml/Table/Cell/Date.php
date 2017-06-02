<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class THtml_Table_Cell_Date extends THtml_Table_Cell{

        /**
         * @param string $value
         * @param array  $args
         *
         * @return false|string
         */
        public static function format($value, $args = array()){
            if($value && $value != '0000-00-00 00:00:00')
                return date_format(new DateTime($value), 'd/m/Y H:i:s');

            return " - ";
        }

    }