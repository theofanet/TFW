<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */
    class TFW_Log extends TFW_Abstract{
        const EXTENSION  = "log";
        const DIRECTORY  = ROOT_PATH.TFW_IO::DS."var".TFW_IO::DS."log";
        const MAX_LENGTH = 50000;

        /**
         * @param mixed  $data
         * @param string $file
         */
        public static function log($data, $file = "sys"){
            $file_path = self::DIRECTORY.TFW_IO::DS.$file.".".self::EXTENSION;
            $fileRaw   = new TFile_Base($file_path);
            $fileRaw   = self::_checkFileSplit($fileRaw);


            $date = time();

            $user = "-";
            if(TFW_Core::getUser())
                $user = TFW_Core::getUser()->first_name." ".TFW_Core::getUser()->last_name;

            if(!is_string($data))
                $data = addslashes(serialize($data));

            $trace = "";
            $backtrace = debug_backtrace();

            if(count($backtrace) > 1){
                array_shift($backtrace);
                $backtrace = $backtrace[0];
                $file = "";
                if($backtrace["file"])
                    $file = str_replace(ROOT_PATH.TFW_IO::DS, "", $backtrace["file"]);
                $line  = isset($backtrace["line"]) ? $backtrace["line"] : "";
                $class = isset($backtrace["class"]) ? $backtrace["class"] : "";
                $func  = isset($backtrace["function"]) ? $backtrace["function"] : "";
                $type  = isset($backtrace["type"]) ? $backtrace["type"] : "";
                $args  = "";
                if(isset($backtrace["args"])){
                    $args_data = array_map(function($arg){return str_replace(ROOT_PATH.TFW_IO::DS, "", $arg);}, $backtrace["args"]);
                    $args      = implode(", ", $args_data);
                }

                $trace = "[$file:$line] $class$type$func($args)";
            }

            $line = "$user;$date;$trace;$data".PHP_EOL;
            $fileRaw->addContent($line);
        }

        /**
         * @return array
         */
        public static function getLogFiles(){
            return TFW_IO::getFiles(self::DIRECTORY, self::EXTENSION);
        }

        /**
         * @param string $file
         *
         * @return array
         */
        public static function getData($file){
            $file_path = self::DIRECTORY.TFW_IO::DS.$file.".".self::EXTENSION;
            $csvRaw    = new TFile_Csv($file_path);

            return $csvRaw->getData();
        }

        /**
         * @param string $file
         */
        public static function remove($file){
            $file_path = self::DIRECTORY.TFW_IO::DS.$file.".".self::EXTENSION;
            $csvRaw    = new TFile_Base($file_path);
            $csvRaw->remove();
        }

        /**
         * @param string $file
         */
        public static function download($file){
            $file_path = self::DIRECTORY.TFW_IO::DS.$file.".".self::EXTENSION;
            $csvRaw    = new TFile_Base($file_path);
            $csvRaw->download();
        }

        /**
         * @param \TFile_Base $file
         *
         * @return \TFile_Base
         */
        private static function _checkFileSplit(TFile_Base $file){
            $fileSize = $file->fileSize();
            $fileName = $file->fileName(true);

            if($fileSize > self::MAX_LENGTH){
                $i = 1;

                $filePath = self::DIRECTORY.TFW_IO::DS.$fileName.'.'.$i.'.'.self::EXTENSION;
                while(file_exists($filePath)){
                    $i++;
                    $filePath = self::DIRECTORY.TFW_IO::DS.$fileName.'.'.$i.'.'.self::EXTENSION;
                }

                $file->moveTo($filePath);
                $return = new TFile_Base(self::DIRECTORY.TFW_IO::DS.$fileName.".".self::EXTENSION);
            }
            else
                $return = $file;

            return $return;
        }

    }