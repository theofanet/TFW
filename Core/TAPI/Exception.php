<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class TAPI_Exception extends TFW_Exception{
        /**
         * Exception output to screen
         */
        public function output(){
            TAPI_Result::return(["error" => $this->getMessage()], $this->getCode());
        }
    }