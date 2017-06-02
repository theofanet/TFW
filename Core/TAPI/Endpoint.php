<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class TAPI_Endpoint extends TFW_Abstract{
        protected $_rights = [];

        /**
         * @param string $name
         * @param array  $arguments
         *
         * @return mixed
         * @throws \TAPI_Exception
         */
        public function exec($name, $arguments){
            if(isset($this->_rights[$name])){
                if(!TAPI_Core::getApiKey()->hasRight($this->_rights[$name]))
                    throw new TAPI_Exception("Unauthorized call", 401);
            }

            try{
                return call_user_func_array([$this, $name], $arguments);
            }
            catch(TAPI_Exception $e){
                throw $e;
            }
        }
    }