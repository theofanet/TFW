<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    abstract class TFW_Login extends TFW_Controller{

        /**
         * Method to try connexion.
         * Must be overwrote in module
         * and inform it in template configuration
         *
         * If user connected returns it to be registered by core
         *
         * @return bool | mixed
         * Returns false if not connected, user data if connected
         */
        abstract public function tryConnect();

    }