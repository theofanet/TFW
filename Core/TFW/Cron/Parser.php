<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 23/02/2017
     */
    class TFW_Cron_Parser extends TFW_Abstract{
        private static $_expressions;

        const IDX_MINUTE  = 0;
        const IDX_HOUR    = 1;
        const IDX_DAY     = 2;
        const IDX_MONTH   = 3;
        const IDX_WEEKDAY = 4;
        const IDX_YEAR    = 5;

        static private $_ranges		= array(
            self::IDX_MINUTE		=> array( 'min'	=> 0,
                'max'	=> 59	),	// Minutes
            self::IDX_HOUR		=> array( 'min'	=> 0,
                'max'	=> 23	),	// Hours
            self::IDX_DAY			=> array( 'min'	=> 1,
                'max'	=> 31	),	// Days
            self::IDX_MONTH		=> array( 'min'	=> 1,
                'max'	=> 12	),	// Months
            self::IDX_WEEKDAY		=> array( 'min'	=> 0,
                'max'	=> 7	)	// Weekdays
        );

        static private $_intervals	= array(
            '@yearly'	=> '0 0 1 1 *',
            '@annualy'	=> '0 0 1 1 *',
            '@monthly'	=> '0 0 1 * *',
            '@weekly'	=> '0 0 * * 0',
            '@midnight'	=> '0 0 * * *',
            '@daily'	=> '0 0 * * *',
            '@hourly'	=> '0 * * * *'
        );

        static private $_keywords	= array(
            self::IDX_MONTH => array(
                '/(january|januar|jan)/i'			=> 1,
                '/(february|februar|feb)/i'			=> 2,
                '/(march|maerz|märz|mar|mae|mär)/i'	=> 3,
                '/(april|apr)/i'				    => 4,
                '/(may|mai)/i'					    => 5,
                '/(june|juni|jun)/i'				=> 6,
                '/(july|juli|jul)/i'				=> 7,
                '/(august|aug)/i'				    => 8,
                '/(september|sep)/i'				=> 9,
                '/(october|oktober|okt|oct)/i'		=> 10,
                '/(november|nov)/i'				    => 11,
                '/(december|dezember|dec|dez)/i'	=> 12
            ),

            self::IDX_WEEKDAY => array(
                '/(sunday|sonntag|sun|son|su|so)/i'		 => 0,
                '/(monday|montag|mon|mo)/i'			     => 1,
                '/(tuesday|dienstag|die|tue|tu|di)/i'	 => 2,
                '/(wednesdays|mittwoch|mit|wed|we|mi)/i' => 3,
                '/(thursday|donnerstag|don|thu|th|do)/i' => 4,
                '/(friday|freitag|fre|fri|fr)/i'		 => 5,
                '/(saturday|samstag|sam|sat|sa)/i'		 => 6
            )
        );

        /**
         * @param string $expression
         *
         * @throws \TFW_Exception
         */
        static public function parse($expression){
            $dummy = array();

            if (substr($expression, 0, 1) == '@') {
                $expression	= strtr($expression, self::$_intervals);
                if (substr($expression,0,1) == '@') {
                    throw new TFW_Exception('Unknown named interval ['.$expression.']', 10000);
                }
            }

            $cron = explode(' ', $expression);

            if(count($cron) != 5) {
                throw new TFW_Exception('Wrong number of segments in expression. Expected: 5, Found: '.count($cron), 10001);
            } else {
                foreach($cron as $idx => $segment) {
                    try {
                        $dummy[$idx] = self::expandSegment($idx, $segment);
                    } catch(TFW_Exception $e) {
                        throw $e;
                    }
                }
            }

            self::$_expressions[$expression] = $dummy;
            self::$_expressions['reverse'][$expression] = self::arrayReverse($dummy);
        }

        /**
         * @param int    $idx
         * @param string $segment
         *
         * @return array
         * @throws \TFW_Exception
         */
        static private function expandSegment($idx, $segment) {
            $osegment = $segment;

            if(isset(self::$_keywords[$idx])) {
                $segment = preg_replace(
                    array_keys(self::$_keywords[$idx]),
                    array_values(self::$_keywords[$idx]),
                    $segment
                );
            }

            if (substr($segment, 0, 1) == '*') {
                $segment = preg_replace(
                    '/^\*(\/\d+)?$/i',
                    self::$_ranges[$idx]['min'].'-'.self::$_ranges[$idx]['max'].'$1',
                    $segment
                );
            }

            $dummy = preg_replace('/[0-9\-\/\,]/','',$segment);

            if(!empty($dummy))
                throw new TFW_Exception('Failed to parse segment: '.$osegment, 10002);

            $result	= array();
            $atoms	= explode(',', $segment);

            foreach($atoms as $curatom)
                $result	= array_merge($result, self::parseAtom($curatom));

            $result	= array_unique($result);
            sort($result);

            if($idx == self::IDX_WEEKDAY) {
                if(end($result) == 7) {
                    if (reset($result) != 0)
                        array_unshift($result, 0);

                    array_pop($result);
                }
            }

            foreach ($result as $key=>$value){
                if (($value < self::$_ranges[$idx]['min']) || ($value > self::$_ranges[$idx]['max']))
                    throw new TFW_Exception('Failed to parse segment, invalid value ['.$value.']: '.$osegment, 10003);
            }

            return $result;
        }

        /**
         * @param string $atom
         *
         * @return array
         */
        static private function parseAtom($atom) {
            $expanded = array();

            if(preg_match('/^(\d+)-(\d+)(\/(\d+))?/i', $atom, $matches)) {
                $low	= $matches[1];
                $high	= $matches[2];

                if($low > $high)
                    list($low,$high) = array($high,$low);

                $step = isset($matches[4]) ? $matches[4] : 1;

                for($i = $low; $i <= $high; $i += $step)
                    $expanded[]	= intval($i);

            } else
                $expanded[]	= intval($atom);

            return $expanded;
        }

        /**
         * @param array $cron
         *
         * @return mixed
         */
        static private function arrayReverse($cron) {
            foreach($cron as $key => $value)
                $cron[$key]	= array_reverse($value);

            return $cron;
        }

        /**
         * @param string $expression
         * @param bool   $reverse
         *
         * @return bool|mixed
         */
        static public function getParsedExpression($expression, $reverse = false){
            if(!$reverse && isset(self::$_expressions[$expression]))
                return self::$_expressions[$expression];
            else if($reverse && isset(self::$_expressions["reverse"][$expression]))
                return self::$_expressions["reverse"][$expression];

            return false;
        }
    }