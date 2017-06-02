<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class THtml_Table_Filter_Number extends THtml_Table_Filter_Text{

        protected $_operator  = array('>', '<', '>=', '<=', '!=');

        const NUMBER_REGEX = '/[0-9]+/';

        /**
         * @param string $value
         *
         * @return array|bool
         */
        public function format($value){
            $value = htmlspecialchars_decode($value);
            $parts = explode(' ', $value);

            $count = count($parts);

            if($count == 1) {
                preg_match(self::NUMBER_REGEX, $value, $matches);
                if(isset($matches[0]))
                    return $matches[0];
            }
            elseif($count == 2 && in_array($parts[0], $this->_operator)) {
                preg_match(self::NUMBER_REGEX, $parts[1], $ex);

                if(isset($ex[0]))
                    return array($ex[0], $parts[0]);
            }
            elseif($count == 3 && $parts[1] == "-"){
                preg_match(self::NUMBER_REGEX, $parts[0], $A);
                preg_match(self::NUMBER_REGEX, $parts[2], $B);

                if(isset($A[0]) && isset($B[0])){
                    $range = '"'.$A[0].'" AND "'.$B[0].'"';
                    return array($range, 'BETWEEN');
                }
            }


            return false;
        }

    }