<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 28/03/2017
     */
    class Core_Helper_Price extends TFW_Helper{

        public function rounder($value, $sep = '.', $decimal_space = ' '){
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

            return number_format($buffer, 2, $sep, $decimal_space);
        }

    }