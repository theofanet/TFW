<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 24/02/2017
     */
    class Core_Controller_Setting_Log extends TFW_Controller{
        protected $_rights = [
            "index"    => "Core:logs:see",
            "delete"   => "Core:logs:delete",
            "download" => "Core:logs:download"
        ];

        /**
         * @param bool|string $log_file
         */
        public function index($log_file = false){
            $args = [];
            if($log_file)
                $args["log_file"] = $log_file;

            $this->setSideBlock("Core/Settings_Log_Menu", $args);
            $this->render("Core/Settings_Log", $args);
        }

        /**
         * @param string $log_file
         *
         * @throws \TFW_Exception
         */
        public function delete($log_file){
            if($log_file){
                TFW_Log::remove($log_file);
                self::goToRoute("/logs");
            }
            else
                throw new TFW_Exception("Missing log file");
        }

        /**
         * @param string $log_file
         *
         * @throws \TFW_Exception
         */
        public function download($log_file){
            if($log_file)
                TFW_Log::download($log_file);
            else
                throw new TFW_Exception("Missing log file");
        }
    }