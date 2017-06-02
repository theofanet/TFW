<?php

    /**
     * Created by Theo.
     */
    class THtml_Table_Cell_Currency extends THtml_Table_Cell{

        /**
         * @param string|mixed $value
         * @param array        $args
         *
         * @return mixed
         */
        public static function format($value, $args = []){
            $type = ' &euro';
            if(isset($args["type"]))
                $type = $args["type"];

            $rounded_value = TFW_Registry::getHelper('Core/Math')->rounder($value);
            $currency      = $type;

            return $rounded_value.$currency;
        }

    }