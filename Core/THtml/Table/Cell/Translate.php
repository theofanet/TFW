<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class THtml_Table_Cell_Translate extends THtml_Table_Cell{

        public static function format($value, $args = []){
            return TFW_Local::translate($value, [], (isset($args["namespace"]) ? $args["namespace"] : false));
        }

        public static function getStyle($value, $args = []){
            if(!isset($args["color"]))
                $args["color"] = "blue";

            return "color:".$args["color"];
        }

    }