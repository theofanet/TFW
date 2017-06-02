<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    class TDB_Mysql extends TFW_Abstract{
        protected $_id   = null;

        protected $_host = null;
        protected $_user = null;
        protected $_pass = null;
        protected $_base = null;

        protected $_charset = null;

        /**
         * @return array
         */
        public function __debugInfo(){
            return [];
        }

        /**
         * @return mysqli
         */
        public function getId(){
            return $this->_id;
        }

        /**
         * TDB_Mysql constructor.
         *
         * @param string $host
         * @param string $user
         * @param string $pass
         * @param string $base
         * @param string $charset
         */
        public function __construct($host, $user, $pass, $base, $charset){
            $this->_host = $host;
            $this->_user = $user;
            $this->_pass = $pass;
            $this->_base = $base;

            $this->_charset = $charset;

            $this->connect();
        }

        /**
         * @throws \TFW_Exception
         */
        public function connect(){
            $this->_id = mysqli_connect(
                $this->_host,
                $this->_user,
                $this->_pass,
                $this->_base
            );

            if(!$this->_id)
                throw new TFW_Exception("DB Connextion error : ".mysqli_connect_error(), mysqli_connect_errno());

            //Setting charset
            try{
                $this->execQuery("SET NAMES '".$this->_charset."'");
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @throws \TFW_Exception
         */
        public function close(){
            if($this->_id){
                if(!mysqli_close($this->_id))
                    throw new TFW_Exception("Unable to close DB : ".mysqli_error($this->_id));
            }
            else
                throw new TFW_Exception("Unable to close DB : Not connected");
        }

        /**
         * @param string|array $q
         *
         * @return mysqli_result|array
         * @throws \TFW_Exception
         */
        public function execQuery($q){
            if(!$this->_id) throw new TFW_Exception("DB Not connected");

            $result  = array();

            $query = $q;
            //if(!is_array($queries))
            //    $queries = explode(';', $queries);

            //foreach($queries as $query){
                if(!empty($query)){
                    $q_rez = mysqli_query($this->_id, $query);
                    if(!$q_rez) throw new TFW_Exception(mysqli_error($this->_id).PHP_EOL.$query, mysqli_errno($this->_id));
                    $result[] = $q_rez;
                }
            //}

            if(count($result) == 1)
                return $result[0];
            else
                return $result;
        }

        /**
         * @return int|string
         * @throws TFW_Exception
         */
        public function getLastId(){
            if(!$this->_id) throw new TFW_Exception("DB Not connected");
            return mysqli_insert_id($this->_id);
        }

        /**
         * @param mysqli_result $queryResults
         * @param bool          $asObject
         * @param bool          $justFirst
         *
         * @return array|bool|mixed
         * @throws \TFW_Exception
         */
        public function fetchResults($queryResults, $asObject = true, $justFirst = false){
            if(!$this->_id) throw new TFW_Exception("DB Not connected");

            $result = array();

            while($r = $asObject ? mysqli_fetch_object($queryResults) : mysqli_fetch_assoc($queryResults)){
                $result[] = $r;
            }

            if($justFirst && count($result))
                return $result[0];
            else if($justFirst)
                return false;

            return $result;
        }

        /**
         * @param string $query
         * @param bool   $asObject
         * @param bool   $justFirst
         *
         * @return array|bool|mixed
         * @throws \TFW_Exception
         */
        public function getResults($query, $asObject = true, $justFirst = false){
            try{
                $q_rez = $this->execQuery($query);

                if(!is_bool($q_rez))
                    return $this->fetchResults($q_rez, $asObject, $justFirst);

                return $q_rez;
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

    }