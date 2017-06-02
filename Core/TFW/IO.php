<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    class TFW_IO extends TFW_Abstract{

        const DS = DIRECTORY_SEPARATOR;

        /**
         * @param string $filename
         *
         * @return string
         */
        private static function _getExt($filename){
            $a = explode('.', $filename);
            return $a[count($a) - 1];
        }

        /**
         * @param string        $dir_name
         * @param bool          $show_dir
         * @param bool          $show_files
         * @param null|string   $ext
         *
         * @return array
         * @throws \TFW_Exception
         */
        public static function getContent($dir_name, $show_dir = true, $show_files = true, $ext = null){
            $result = array();

            if(is_dir($dir_name)){
                try{
                    $dir = opendir($dir_name);
                    while($file = readdir($dir)){
                        if($file != '.' && $file != '..' && $file != '.DS_Store'){
                            $fname  = $dir_name.self::DS.$file;
                            $is_dir = is_dir($fname);
                            if(($show_dir && $is_dir || ($show_files && !$is_dir && (!$ext || self::_getExt($file) == $ext))))
                                $result[$file] = $file;
                        }
                    }
                    closedir($dir);
                }
                catch(Exception $e){
                    throw new TFW_Exception($e->getMessage(), $e->getCode());
                }
            }
            else
                throw new TFW_Exception($dir_name." is not a directory");

            return $result;
        }

        /**
         * @param string       $dir_name
         * @param null|string  $ext
         *
         * @return array
         * @throws \TFW_Exception
         */
        public static function getFiles($dir_name, $ext = null){
            try{
                return self::getContent($dir_name, false, true, $ext);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param string $dir_name
         *
         * @return array
         * @throws \TFW_Exception
         */
        public static function getDirectories($dir_name){
            try{
                return self::getContent($dir_name, true, false);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

    }