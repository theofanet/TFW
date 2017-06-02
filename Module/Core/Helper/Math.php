<?php

    /**
     * Created by Theo.
     */
    class Core_Helper_Math extends TFW_Helper{

        /**
         * @param number $value
         * @param string $sep
         *
         * @return string
         */
        public function rounder($value, $sep = '.'){
            $buffer    = $value * 100;
            $rounded   = round($buffer);
            $buffer    = $rounded / 100;
            $point_pos = strpos($buffer, ".");

            if($point_pos == FALSE)
                $buffer .= ".00";
            else{
                if((strlen($buffer) - $point_pos) == 2)
                    $buffer .= "0";
            }

            return number_format($buffer, 2, $sep, ' ');
        }
    }