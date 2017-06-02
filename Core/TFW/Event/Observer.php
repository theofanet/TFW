<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class TFW_Event_Observer extends TFW_Abstract{

        /**
         * Will trigger observer event action if exists
         *
         * @param \TFW_Event $event
         */
        public function triggerEvent(TFW_Event $event){
            if(method_exists($this, $event->getAction()))
                call_user_func_array(array($this, $event->getAction()), $event->getArgs());
        }

    }