<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class Core_THtml_Table_Cell_Cron_Status extends THtml_Table_Cell{

        /***
         * @param Core_Model_Cron $cron
         *
         * @return string
         */
        public static function format($cron){
            $class   = "success";
            $label   = "Running";
            $tooltip = "";

            if($cron->status == TFW_Cron::STATUS_ERROR){
                $class   = "danger";
                $label   = "Error";
                $tooltip = "data-toggle=\"tooltip\" data-placement=\"bottom\" title=\"$cron->error_message\"";
            }
            else if($cron->status == TFW_Cron::STATUS_DISABLE){
                $class = "default";
                $label = "Disabled";
            }

            return "<span class=\"label label-$class\" $tooltip>$label</span>";
        }

    }