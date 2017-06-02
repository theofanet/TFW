<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 02/03/2017
     */
    class TFW_Rest extends TFW_Abstract{
        /**
         * @var string $_url
         */
        private $_url = '';

        /**
         * @var array $_headers
         */
        private $_headers = array();

        private $_responseCode = 200;

        /**
         * TFW_Rest constructor.
         *
         * @param string $baseUrl
         */
        public function __construct($baseUrl){
            $this->_url = rtrim($baseUrl, "/");
        }

        /**
         * @return int
         */
        public function getResponseCode(){
            return $this->_responseCode;
        }

        /**
         * @param array $h
         *
         * @return $this
         */
        public function setHeaders($h){
            if(is_array($h))
                $this->_headers = $h;
            return $this;
        }

        /**
         * @param string $key
         * @param string $value
         *
         * @return $this
         */
        public function addHeader($key, $value){
            $this->_headers[$key] = "$key: $value";
            return $this;
        }

        /**
         * @param string $endpoint
         * @param array  $args
         *
         * @return mixed
         */
        public function post($endpoint, $args = array()){
            $curl = curl_init();

            $endpoint = trim($endpoint, "/");
            curl_setopt($curl, CURLOPT_URL, $this->_url.'/'.$endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($args));

            $results = curl_exec($curl);

            return $this->_handleResults($curl, $results);
        }



        /**
         * @param string $endpoint
         * @param array  $args
         *
         * @return mixed
         */
        public function get($endpoint, $args = array()){
            $get_args = '';

            foreach($args as $k => $v)
                $get_args .= $k.'='.$v.'&';
            if(strlen($get_args)){
                $get_args = substr($get_args, 0, strlen($get_args) - 1);
                $get_args = '?'.$get_args;
            }

            $curl = curl_init();
            $endpoint = trim($endpoint, "/");

            curl_setopt($curl, CURLOPT_URL, $this->_url.'/'.$endpoint.$get_args);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

            $results = curl_exec($curl);

            return $this->_handleResults($curl, $results);
        }

        /**
         * @param $curl
         * @param $results
         *
         * @return mixed
         */
        private function _handleResults($curl, $results){
            $this->_responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

            curl_close($curl);

            /**
             * TODO: Handle errors
             */
            if($this->_responseCode != 200 && $this->_responseCode != 206)
                self::dump($results);

            return TAPI_Format::decode($results, $type);
        }
    }