<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 02/03/2017
     */
    class TFW_Core_Event extends TFW_Event_Subject{
        /*
         * Events
         */
        const MODULES_LOADED = "modulesLoaded";
        const INIT_DONE      = "initDone";

        public function __construct(){
            $this->attach(TFW_Event_Dispatcher::getInstance());
        }
    }