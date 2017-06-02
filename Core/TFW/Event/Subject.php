<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 15/02/2017
     */
    class TFW_Event_Subject extends TFW_Abstract implements SplSubject{

        private $_observers    = array();
        private static $_event = false;

        /**
         * @param \SplObserver $observer
         *
         * @return $this
         */
        public function attach(SplObserver $observer){
            if(!is_int($key = array_search($observer, $this->_observers, true)))
                $this->_observers[] = $observer;

            return $this;
        }

        /**
         * @param \SplObserver $observer
         *
         * @return $this
         */
        public function detach(SplObserver $observer){
            if(is_int($key = array_search($observer, $this->_observers, true)))
                unset($this->_observers[$key]);

            return $this;
        }

        /**
         * Notify all observers with event
         */
        public function notify(){
            if(self::$_event){
                foreach($this->_observers as $obs)
                    $obs->update($this);
            }
        }

        /**
         * @param string $e
         * @param mixed  $data
         */
        public function triggerEvent($e, $data = []){
            if(!is_array($data))
                $data = [$data];
            $event = new TFW_Event($e, $data);
            self::$_event = $event;
            $this->notify();
        }

        /**
         * @return bool|string
         */
        public static function getEvent(){
            return self::$_event;
        }

    }