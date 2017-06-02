<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    class TFW_Exception extends Exception{

        /**
         * Exception output to screen
         */
        public function output(){
            /*
             * TODO : Save to file error report
             */
            $file    = str_replace(ROOT_PATH, "", $this->getFile());
            $line    = $this->getLine();
            $message = $this->getMessage();

            echo "[$file:$line] $message";
        }
    }