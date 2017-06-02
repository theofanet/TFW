<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 01/03/2017
     */
    class TAPI_Format extends TFW_Abstract{
        private static $_formats = [
            "application/json"
        ];

        /**
         * @param string $format
         *
         * @return bool
         */
        public static function isAvailable($format){
            return in_array($format, self::$_formats);
        }

        /**
         * @param string $data
         * @param string $format
         *
         * @return array|string
         * @throws \TAPI_Exception
         */
        public static function decode($data, $format){
            if(in_array($format, self::$_formats)){
                $parts = explode("/", $format);
                if(count($parts) > 1){
                    $f = ucfirst($parts[1]);
                    if(method_exists("TAPI_Format", "_decode$f"))
                        return self::checkValues(call_user_func_array(["TAPI_Format", "_decode$f"], [$data]));
                    else
                        throw new TAPI_Exception("Decode method not found for format $format", 500);
                }
                else
                    throw new TAPI_Exception("Error with format", 500);
            }

            return $data;
        }

        /**
         * @param mixed  $data
         * @param string $format
         *
         * @return mixed
         * @throws \TAPI_Exception
         */
        public static function encode($data, $format){
            if(in_array($format, self::$_formats)){
                $parts = explode("/", $format);
                if(count($parts) > 1){
                    $f = ucfirst($parts[1]);
                    if(method_exists("TAPI_Format", "_encode$f"))
                        return call_user_func_array(["TAPI_Format", "_encode$f"], [$data]);
                    else
                        throw new TAPI_Exception("Encode method not found for format $format", 500);
                }
                else
                    throw new TAPI_Exception("Error with format", 500);
            }
            else
                throw new TAPI_Exception("Format no available : $format");
        }

        /**
         * @param string $data
         *
         * @return mixed
         */
        private static function _decodeJson($data){
            return json_decode($data, true);
        }

        /**
         * @param mixed $data
         *
         * @return string
         */
        private static function _encodeJson($data){
            return json_encode($data);
        }
    }