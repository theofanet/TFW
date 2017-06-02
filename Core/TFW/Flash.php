<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    class TFW_Flash extends TFW_Abstract{

        public static function addFlash($content, $title, $class, $type){
            $flashes = TFW_Registry::getVar("flashes");
            if(!$flashes)
                $flashes = ["toastr" => [], "alerts" => []];
            $flashes[$type][] = [$content, $title, $class];
            TFW_Registry::setVar("flashes", $flashes);
        }

        public static function getFlashes(){
            return TFW_Registry::getVar("flashes:toastr", true);
        }

        public static function getAlertsFlash(){
            return TFW_Registry::getVar("flashes:alerts", true);
        }

        public static function addSuccess($content, $title = ""){
            self::addFlash($content, $title, "success", "toastr");
        }

        public static function addError($content, $title = ""){
            self::addFlash($content, $title, "error", "toastr");
        }

        public static function addWarning($content, $title = ""){
            self::addFlash($content, $title, "warning", "toastr");
        }

        public static function addInfo($content, $title = ""){
            self::addFlash($content, $title, "info", "toastr");
        }

        public static function addSuccessAlert($content, $title = ""){
            self::addFlash($content, $title, "success", "alerts");
        }

        public static function addErrorAlert($content, $title = ""){
            self::addFlash($content, $title, "danger", "alerts");
        }

        public static function addWarningAlert($content, $title = ""){
            self::addFlash($content, $title, "warning", "alerts");
        }

        public static function addInfoAlert($content, $title = ""){
            self::addFlash($content, $title, "info", "alerts");
        }

    }