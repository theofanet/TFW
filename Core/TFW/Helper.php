<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    abstract class TFW_Helper extends TFW_Abstract{
        /**
         * @param $method
         * @param $arguments
         *
         * @throws \TFW_Exception
         */
        function __call($method, $arguments){
            if(method_exists($this, $method))
                call_user_func_array(array($this, $method), $arguments);
            else
                throw new TFW_Exception(get_class($this)." - $method : Method not found");
        }
    }