<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class TFW_Event_Dispatcher extends TFW_Abstract implements SplObserver{

        private static $_listener = array();
        private static $_instance = null;

        /**
         * @return \TFW_Event_Dispatcher
         */
        public static function getInstance(){
            if(!self::$_instance)
                self::$_instance = new TFW_Event_Dispatcher();

            return self::$_instance;
        }

        /**
         * Params should be the classes name
         *
         * @param string $observer
         * @param string $subject
         */
        public static function addObserver($observer, $subject){
            if(!isset(self::$_listener[$subject]))
                self::$_listener[$subject] = array();

            self::$_listener[$subject][] = new $observer();
        }

        /**
         * @param \TFW_Event_Subject|SplSubject $subject
         */
        public function update(SplSubject $subject){
            $subject_class = get_class($subject);
            if(isset(self::$_listener[$subject_class])){
                /**
                 * @var TFW_Event_Observer $observer
                 */
                foreach(self::$_listener[$subject_class] as $observer)
                    $observer->triggerEvent($subject->getEvent());
            }
        }

    }