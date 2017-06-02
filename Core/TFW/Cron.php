<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class TFW_Cron extends TFW_Abstract{
        private $_key       = "";
        private $_frequency = "";
        private $_action    = "";

        private $_table = "app_cron_tasks";
        /**
         * @var TDB_Mysql $_db
         */
        private $_db = null;

        const STATUS_SUCCESS = 0;
        const STATUS_ERROR   = 1;
        const STATUS_DISABLE = 2;

        private $_times = [
            "last" => 0,
            "next" => 0
        ];

        /**
         * TFW_Cron constructor.
         * @param string $key
         * @param string $action
         * @param string $frequency
         *
         * @throws \TFW_Exception
         */
        public function __construct($key, $action, $frequency){
            $this->_key = $key;
            $this->_frequency = $frequency;
            $this->_action = $action;
            $this->_times = [
                "last" => 0,
                "next" => time()
            ];

            if(!method_exists($this, $action)) throw new TFW_Exception("Action $action not present in CRON ".get_class($this));

            $this->_db = TFW_Registry::getRegistered("db");
            if(!$this->_db) throw new TFW_Exception(get_class($this)." - DB not initialised");

            try{
                $this->_checkExistence();
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @return string
         */
        public function getKey(){
            return $this->_key;
        }

        /**
         * @param string $what
         *
         * @return int|mixed
         */
        public function getTime($what = "next"){
            if($what == "next" || $what == "last")
                return $this->_times[$what];

            return 0;
        }

        /**
         * @throws \TFW_Exception
         */
        private function _checkExistence(){
            $query = "SELECT * FROM $this->_table WHERE code LIKE \"$this->_key\"";
            try{
                $r = $this->_db->getResults($query, true, true);
                if(!$r)
                    $this->_db->execQuery("INSERT INTO $this->_table (code) VALUE (\"$this->_key\")");
                else{
                    $this->_times["next"] = ($r->next_execution_time == "0000-00-00 00:00:00" ? time() : $r->next_execution_time);
                    $this->_times["last"] = $r->last_execution_time;
                }
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            $this->_times["next"] = TFW_Registry::getHelper('Core/Time')->getDateTimeObject($this->_times["next"]);
            $this->_times["last"] = TFW_Registry::getHelper('Core/Time')->getDateTimeObject($this->_times["last"]);
        }

        /**
         * @throws \TFW_Exception
         */
        public function execute(){
            $this->_last_error = false;

            try{
                call_user_func([$this, $this->_action]);
            }
            catch(TFW_Exception $e){
                $this->_last_error = $e->getMessage();
            }

            /**
             * @var Core_Helper_Time $helper
             * @var DateTime $d
             */
            $helper = TFW_Registry::getHelper('Core/Time');
            $d      = $helper->getDateTimeObject(time());
            $d->add(new DateInterval('PT1M'));
            $next_execution_time = $helper->getDateTime($this->getNextOccurrence($this->_frequency, $d->getTimestamp()));

            $query = "UPDATE $this->_table SET last_execution_time=NOW(), next_execution_time=\"$next_execution_time\"";

            if($this->_last_error !== false)
                $query .= ", status=".self::STATUS_ERROR.", error_message=\"".self::checkValues($this->_last_error)."\"";
            else
                $query .= ", status=".self::STATUS_SUCCESS.", error_message=\"\"";

            $query .= " WHERE code LIKE \"$this->_key\"";

            try{
                $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param string $message
         *
         * @throws \TFW_Exception
         */
        public function setAsError($message){
            $this->_last_error = $message;
        }

        /**
         * @param string|int $currentTime
         *
         * @return bool
         */
        public function canExecute($currentTime = 'now'){
            if('now' === $currentTime)
                $currentTime = time();
            elseif ($currentTime instanceof DateTime) {
                $currentDate = clone $currentTime;
                $currentDate->setTimezone(new DateTimeZone(date_default_timezone_get()));
                $currentDate = $currentDate->format('Y-m-d H:i');
                $currentTime = strtotime($currentDate);
            } else {
                $currentTime = new DateTime($currentTime);
                $currentTime->setTime($currentTime->format('H'), $currentTime->format('i'), 0);
                $currentTime = $currentTime->getTimestamp();
            }

            try {
                return $this->_times["next"]->getTimestamp() <= $currentTime;
            } catch(Exception $e) {
                return false;
            }
        }

        /**
         * @param string          $expression
         * @param null|int|string $timestamp
         *
         * @return false|int
         * @throws \TFW_Exception
         */
        static public function getNextOccurrence($expression, $timestamp = null) {
            try {
                $next = self::getTimestamp($timestamp);
                $next_time = self::calculateDateTime($expression, $next);
            } catch (TFW_Exception $e) {
                throw $e;
            }

            return $next_time;
        }

        /**
         * @param string          $expression
         * @param null|int|string $timestamp
         *
         * @return false|int
         * @throws \TFW_Exception
         */
        static public function getLastOccurrence($expression, $timestamp = null) {
            try {
                $last = self::getTimestamp($timestamp);
                $last_time = self::calculateDateTime($expression, $last, false);
            } catch (TFW_Exception $e) {
                throw $e;
            }

            return $last_time;
        }

        /**
         * @param array $rtime
         *
         * @return array
         */
        static private function cleanRTime($rtime){
            $result =  array();

            foreach($rtime as $key => $value){
                if(is_array($value))
                    $result[$key] = self::cleanRTime($rtime);
                else
                    $result[$key] = intval($value);
            }

            return $result;
        }

        /**
         * @param string $expression
         * @param array  $rtime
         * @param bool   $next
         *
         * @return false|int
         * @throws \TFW_Exception
         */
        static private function calculateDateTime($expression, $rtime, $next = true) {
            $calc_date	= true;
            $cron		= self::getExpression($expression, !$next);

            if (!in_array($rtime[TFW_Cron_Parser::IDX_DAY], $cron[TFW_Cron_Parser::IDX_DAY]) ||
                !in_array($rtime[TFW_Cron_Parser::IDX_MONTH], $cron[TFW_Cron_Parser::IDX_MONTH]) ||
                !in_array($rtime[TFW_Cron_Parser::IDX_WEEKDAY], $cron[TFW_Cron_Parser::IDX_WEEKDAY])) {
                $rtime[TFW_Cron_Parser::IDX_HOUR]	= reset($cron[TFW_Cron_Parser::IDX_HOUR]);
                $rtime[TFW_Cron_Parser::IDX_MINUTE]	= reset($cron[TFW_Cron_Parser::IDX_MINUTE]);
            }
            else {
                $nhour = self::findValue($rtime[TFW_Cron_Parser::IDX_HOUR], $cron[TFW_Cron_Parser::IDX_HOUR], $next);

                if($nhour === false) {
                    $rtime[TFW_Cron_Parser::IDX_HOUR]	= reset($cron[TFW_Cron_Parser::IDX_HOUR]);
                    $rtime[TFW_Cron_Parser::IDX_MINUTE]	= reset($cron[TFW_Cron_Parser::IDX_MINUTE]);

                    $rtime = explode(',', strftime('%M,%H,%d,%m,%w,%Y', mktime($rtime[TFW_Cron_Parser::IDX_HOUR], $rtime[TFW_Cron_Parser::IDX_MINUTE], 0, $rtime[TFW_Cron_Parser::IDX_MONTH], $rtime[TFW_Cron_Parser::IDX_DAY], $rtime[TFW_Cron_Parser::IDX_YEAR]) + ((($next) ? 1 : -1) * 86400)));
                    $rtime = self::cleanRTime($rtime);
                }
                else {
                    $nminute = self::findValue($rtime[TFW_Cron_Parser::IDX_MINUTE], $cron[TFW_Cron_Parser::IDX_MINUTE], $next);

                    if(!$nminute) {

                        $nhour = self::findValue($rtime[TFW_Cron_Parser::IDX_HOUR] + (($next) ? 1 : -1), $cron[TFW_Cron_Parser::IDX_HOUR], $next);

                        if ($nhour === false) {
                            $nminute = reset($cron[TFW_Cron_Parser::IDX_MINUTE]);
                            $nhour	 = reset($cron[TFW_Cron_Parser::IDX_HOUR]);
                            $rtime	 = explode(',', strftime('%M,%H,%d,%m,%w,%Y', mktime($nhour, $nminute, 0, $rtime[TFW_Cron_Parser::IDX_MONTH], $rtime[TFW_Cron_Parser::IDX_DAY], $rtime[TFW_Cron_Parser::IDX_YEAR]) + ((($next) ? 1 : -1) * 86400)));
                            $rtime   = self::cleanRTime($rtime);
                        }
                        else{
                            $rtime[TFW_Cron_Parser::IDX_HOUR]	= $nhour;
                            $rtime[TFW_Cron_Parser::IDX_MINUTE]	= (($next) ? reset($cron[TFW_Cron_Parser::IDX_MINUTE]) : end($cron[TFW_Cron_Parser::IDX_MINUTE]));

                            $calc_date	= false;
                        }
                    }
                    else{
                        if($nhour != $rtime[TFW_Cron_Parser::IDX_HOUR])
                            $nminute = reset($cron[TFW_Cron_Parser::IDX_MINUTE]);

                        $rtime[TFW_Cron_Parser::IDX_HOUR]	= $nhour;
                        $rtime[TFW_Cron_Parser::IDX_MINUTE]	= $nminute;

                        $calc_date = false;
                    }

                }

            }

            // If we have to calculate the date... we'll do so

            if ($calc_date) {

                if(in_array($rtime[TFW_Cron_Parser::IDX_DAY], $cron[TFW_Cron_Parser::IDX_DAY]) &&
                    in_array($rtime[TFW_Cron_Parser::IDX_MONTH], $cron[TFW_Cron_Parser::IDX_MONTH]) &&
                    in_array($rtime[TFW_Cron_Parser::IDX_WEEKDAY], $cron[TFW_Cron_Parser::IDX_WEEKDAY])) {

                    return mktime($rtime[1], $rtime[0], 0, $rtime[3], $rtime[2], $rtime[5]);

                } else {

                    $cdate	= mktime(0, 0, 0, $rtime[TFW_Cron_Parser::IDX_MONTH], $rtime[TFW_Cron_Parser::IDX_DAY], $rtime[TFW_Cron_Parser::IDX_YEAR]);


                    for ($nyear = $rtime[TFW_Cron_Parser::IDX_YEAR];(($next) ? ($nyear <= $rtime[TFW_Cron_Parser::IDX_YEAR] + 10) : ($nyear >= $rtime[TFW_Cron_Parser::IDX_YEAR] -10));$nyear = $nyear + (($next) ? 1 : -1)) {

                        foreach($cron[TFW_Cron_Parser::IDX_MONTH] as $nmonth) {

                            foreach($cron[TFW_Cron_Parser::IDX_DAY] as $nday) {

                                if(checkdate($nmonth, $nday, $nyear)) {

                                    $ndate = mktime(0, 0, 1, $nmonth, $nday, $nyear);

                                    if(($next) ? ($ndate >= $cdate) : ($ndate <= $cdate)) {

                                        $dow = date('w', $ndate);

                                        if (in_array($dow,$cron[TFW_Cron_Parser::IDX_WEEKDAY])) {

                                            $rtime	= explode(',', strftime('%M,%H,%d,%m,%w,%Y', mktime($rtime[TFW_Cron_Parser::IDX_HOUR], $rtime[TFW_Cron_Parser::IDX_MINUTE], 0, $nmonth, $nday, $nyear)));
                                            $rtime = self::cleanRTime($rtime);

                                            return mktime($rtime[1], $rtime[0], 0, $rtime[3], $rtime[2], $rtime[5]);

                                        }

                                    }

                                }

                            }

                        }

                    }

                }

                throw new TFW_Exception('Failed to find date, No matching date found in a 10 years range!', 10004);

            }

            return mktime($rtime[1], $rtime[0], 0, $rtime[3], $rtime[2], $rtime[5]);

        }

        /**
         * @param null|int|string $timestamp
         *
         * @return array
         */
        static private function getTimestamp($timestamp = null) {
            if(is_null($timestamp))
                $arr = explode(',', strftime('%M,%H,%d,%m,%w,%Y', time()));
            else
                $arr = explode(',', strftime('%M,%H,%d,%m,%w,%Y', $timestamp));

            foreach($arr as $key => $value)
                $arr[$key] = intval($value);

            return $arr;

        }

        /**
         * @param string $value
         * @param array  $data
         * @param bool   $next
         *
         * @return bool|int
         */
        static private function findValue($value, $data, $next = true) {
            if(in_array($value, $data))
                return intval($value);
            else{
                if(($next) ? ($value <= end($data)) : ($value >= end($data))){
                    foreach($data as $curval){
                        if(($next) ? ($value < intval($curval)) : ($curval < $value))
                            return intval($curval);
                    }
                }
            }

            return false;
        }

        /**
         * @param string $expression
         * @param bool   $reverse
         *
         * @return mixed
         * @throws \Exception
         */
        static private function getExpression($expression, $reverse = false) {
            // First of all we cleanup the expression and remove all duplicate tabs/spaces/etc.
            // For example "*              * *    * *" would be converted to "* * * * *", etc.
            $expression	= preg_replace('/(\s+)/', ' ', strtolower(trim($expression)));

            // Lets see if we've already parsed that expression

            if(!TFW_Cron_Parser::getParsedExpression($expression)){
                // Nope - parse it!
                try {
                    TFW_Cron_Parser::parse($expression);
                } catch (Exception $e) {
                    throw $e;
                }

            }

            return TFW_Cron_Parser::getParsedExpression($expression, $reverse);
        }
    }