<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class Core_THtml_Table_Cell_Active extends THtml_Table_Cell{
        /**
         * Default Cell item for status
         * => Element must have a active item
         * 1 : Enabled
         * 0 : Disabled
         *
         * @param mixed|string $element
         * @param array        $args
         *
         * @return string
         */

        public static function format($element, $args = []){
            $class   = "success";
            $label   = "Enabled";

            if(!$element->active){
                $class   = "danger";
                $label   = "Disabled";
            }

            $label = self::__($label);

            return "<span class=\"label label-$class\">$label</span>";
        }

    }