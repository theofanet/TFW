<?php

    /**
     * Created by Theo.
     */
    class Core_Helper_Time extends TFW_Helper{
        protected $_days = array(
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        );

        protected $_months = array(
            'January',
            'February',
            'Mars',
            'Avril',
            'Mai',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        );

        /**
         * @param bool|string|int $timestamp
         *
         * @return string
         */
        function getDateTime($timestamp = false){
            $date = new DateTime(!is_numeric($timestamp) ? $timestamp : 'now');
            $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

            if(is_numeric($timestamp))
                $date->setTimestamp($timestamp);

            return $date->format("Y-m-d H:i:s");
        }

        /**
         * @param bool|string|int $timestamp
         *
         * @return DateTime
         */
        function getDateTimeObject($timestamp = false){
            $date = new DateTime(!is_numeric($timestamp) ? $timestamp : 'now');
            $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

            if(is_numeric($timestamp))
                $date->setTimestamp($timestamp);

            return $date;
        }

        /**
         * @param string|int $time
         * @param string     $format
         *
         * @return string
         */
        public function formatDate($time, $format = "d/m/Y H:i:s"){
            if($time instanceof DateTime)
                $date = $time;
            else{
                str_replace('/', '-', $time);
                $date = new DateTime(!is_numeric($time) ? $time : 'now');
            }

            $date->setTimezone(new DateTimeZone(date_default_timezone_get()));

            if(is_numeric($time))
                $date->setTimestamp($time);

            return $date->format($format);
        }

        /**
         * @param null|string|int $timestamp
         *
         * @return string
         * @throws TFW_Exception
         */
        public function getDateString($timestamp = NULL){
            if($timestamp == NULL)
                $timestamp = time();
            else if(!is_numeric($timestamp))
                $timestamp = strtotime($timestamp);

            $_date = date('N|d|m|Y', $timestamp);
            $_date = explode('|', $_date);

            return $this->__($this->_days[intval($_date[0]) - 1])
            .' '.$_date[1]
            .' '.$this->__($this->_months[intval($_date[2]) - 1])
            .' '.$_date[3];
        }

        /**
         * @param int|string $date
         *
         * @return string
         * @throws TFW_Exception
         */
        public function getExplicitDate($date){
            if(!ctype_digit($date))
                $date = strtotime($date);

            if(date('Ymd', $date) == date('Ymd')){
                $diff = time()-$date;
                if($diff < 60) /* moins de 60 secondes */
                    return $this->__('%d sec ago', $diff);
                else if($diff < 3600) /* moins d'une heure */
                    return $this->__('%d min ago', round($diff/60, 0));
                else if($diff < 10800) /* moins de 3 heures */
                    return $this->__('%d hours ago', round($diff/3600, 0));
                else /*  plus de 3 heures ont affiche ajourd'hui à HH:MM:SS */
                    return $this->__('Today at %s', date('H:i:s', $date));
            }
            else if(date('Ymd', $date) == date('Ymd', strtotime('- 1 DAY')))
                return $this->__('Yesterday at %s', date('H:i:s', $date));
            else if(date('Ymd', $date) == date('Ymd', strtotime('- 2 DAY')))
                return $this->__('Two days ago at %s', date('H:i:s', $date));
            else
                return $this->__('The %s at %s', date('d/m/Y', $date), date('H:i:s', $date));
        }
    }