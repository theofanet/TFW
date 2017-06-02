<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 08/02/2017
     */
    abstract class TDB_Model extends TFW_Event_Subject{
        protected $_table;
        protected $_root_as;

        /**
         * @var TDB_Mysql $_db
         */
        protected $_db;

        /*
         * Events
         */
        const EVENT_CREATED      = "Created";
        const EVENT_MASS_CREATED = "MassCreated";
        const EVENT_DELETED      = "Deleted";
        const EVENT_SAVED        = "Saved";
        const EVENT_LOADED       = "Loaded";

        /*
         * Data
         */
        protected $_data    = array();
        protected $_oldData = array();
        protected $_updated = array();

        protected $_order  = NULL;
        protected $_select = array();
        protected $_where  = array();
        protected $_joints = array();
        protected $_limit  = null;

        protected $_isLoaded = false;
        protected $_idValue  = null;
        protected $_idField  = 'id';


        /**
         * TDB_Model constructor.
         */
        public function __construct(){
            $this->_db = TFW_Registry::getRegistered("db");
            if(!$this->_db)
                throw new TFW_Exception(get_class($this)." - DB not initialised");
            $this->attach(TFW_Event_Dispatcher::getInstance());
            $this->_addJoint();
            if(!$this->_order)
               $this->_order = "$this->_idField DESC";
        }

        /**
         * @param string              $key
         * @param string|array|bool  $value
         *
         * @return $this
         */
        public function addWhere($key, $value = false){
            $db = TFW_Registry::getRegistered("db");
            if($db && $value !== false && is_string($value))
                $value = mysqli_real_escape_string($db->getId(), $value);
            else if($db && $value !== false && is_array($value)){
                if(is_array($value[0])){
                    $value[0][0] = mysqli_real_escape_string($db->getId(), $value[0][0]);
                    $value[0][1] = mysqli_real_escape_string($db->getId(), $value[0][1]);
                }
                else
                    $value[0] = mysqli_real_escape_string($db->getId(), $value[0]);
            }
            $this->_where[] = [$key, $value];
            return $this;
        }

        /**
         * @return string
         */
        public function _getTable(){
            return $this->_table;
        }

        /**
         * @return string
         */
        public static function getTable(){
            $class = get_called_class();
            /**
             * @var TDB_Model $obj
             */
            $obj   = new $class();
            return $obj->_getTable();
        }

        /**
         * @return bool
         */
        public function isLoaded(){
            return $this->_isLoaded;
        }

        /**
         * @return bool
         */
        public function isUpdated(){
            return count($this->_updated);
        }

        /**
         * @return mixed
         */
        public function getId(){
            return $this->_idValue;
        }

        /**
         * @return array
         */
        public function getData(){
            return $this->_data;
        }

        /**
         * @param int $from
         * @param int $nb
         *
         * @return $this
         */
        public function setLimit($from, $nb = 1){
            $this->_limit = [$from, $nb];
            return $this;
        }

        /**
         * @param string $order
         *
         * @return $this
         */
        public function setOrder($order){
            $this->_order = $order;
            return $this;
        }

        /**
         * @param string $name
         * @param mixed  $value
         */
        public function __set($name, $value){
            if(!isset($this->_data[$name]) || (isset($this->_data[$name]) && $this->_data[$name] != $value)){
                if(isset($this->_data[$name]))
                    $this->_oldData[$name] = $this->_data[$name];

                $db = TFW_Registry::getRegistered("db");
                if($db)
                    $value = mysqli_real_escape_string($db->getId(), $value);

                $this->_updated[]   = $name;
                $this->_data[$name] = $value;
            }
        }

        /**
         * @param string $name
         *
         * @return null
         */
        public function __get($name){
            if(isset($this->_data[$name]))
                return stripslashes($this->_data[$name]);

            return null;
        }

        /**
         * Construct the joints query
         *
         * @param array  $tableData
         * @param array  $onList
         * @param array  $select
         * @param string $type
         *
         * @return $this
         * @throws TFW_Exception
         */
        protected function addJoint($tableData, $onList, $select = [], $type = "LEFT OUTER JOIN"){
            if(!(list($table, $tableAs) = $tableData))
                throw new TFW_Exception(get_class($this)." - addJoin error - Table data");

            $query = "$type $table $tableAs ON ";
            foreach($onList as $on => $as){
                $addRoot = (strpos($on, '.') === false && !intval($on));
                $query .= ($addRoot ? $tableAs."." : "").$on;
                if(is_array($as) && count($as) == 2){
                    $addRoot = (strpos($as[0], '.') === false && !intval($as[0]));
                    $query .= $as[1].($addRoot ? $this->_root_as."." : "").$as[0];
                }
                else if($as){
                    $addRoot = (strpos($as, '.') === false && !intval($as));
                    $query .= "=".($addRoot ? $this->_root_as."." : "").$as;
                }
                $query .= " AND ";
            }
            $query = substr($query, 0, strlen($query) - 5);
            $this->_joints[] = $query;

            if(!count($this->_select))
                $this->_select[] = $this->_root_as.".*";

            foreach($select as $s => $as){
                if(substr_count($s, 'CONCAT(')
                    || substr_count($s, 'COUNT(')
                    || substr_count($s, 'IF(')
                    || substr_count($s, 'LPAD(')
                    || substr_count($s, 'SUM(')
                    || substr_count($s, 'RPAD(')
                    || substr_count($s, '-')) {
                    $_select = $s;
                }
                else
                    $_select = $tableAs . '.' . $s;

                $this->_select[] = $_select.' AS '.$as;
            }

            return $this;
        }

        /**
         * Construct WHERE
         *
         * @return string
         */
        private function _constructWhere(){
            $query = "";
            if(count($this->_where)){
                $query .= "WHERE ";

                foreach($this->_where as list($key, $value)){
                    $query .= "$key";

                    if($value !== false){
                        $sign = "=";

                        if(is_array($value) && count($value) == 2){
                            $sign  = $value[1];
                            $v     = $value[0];
                        }
                        else
                            $v = $value;

                        $sign_no_space = str_replace(" ", "", $sign);

                        if(is_array($v) && $sign_no_space == "BETWEEN")
                            $v = "'$v[0]' AND '$v[1]'";
                        else if(is_string($v) && !in_array($sign_no_space, ["IN"]))
                            $v = "'$v'";

                        if($v !== false)
                            $query .= $sign.$v;
                    }

                    $query .= " AND ";
                }

                $query = substr($query, 0, strlen($query) - 5);
            }

            return $query;
        }

        /**
         * Construct loading query
         *
         * @return string
         */
        protected function _constructQuery(){
            $count = count($this->_select);
            if($count){
                $selects = "";
                if($this->_select[0] != $this->_root_as.".*")
                    $selects = $this->_root_as.".".$this->_idField.($count > 1 ? ", " : "");
                $selects .= implode(", ", $this->_select);
            }
            else
                $selects = "*";

            $query = "SELECT $selects FROM $this->_table $this->_root_as";

            if(count($this->_joints)){
                foreach($this->_joints as $joint)
                    $query .= " $joint";
            }

            $query = "SELECT * FROM ($query) AS MASTER ".$this->_constructWhere();

            if($this->_order)
                $query .= " ORDER BY $this->_order";

            if($this->_limit && list($from, $nb) = $this->_limit)
                $query .= " LIMIT $from, $nb";

            return $query;
        }

        /**
         * Loads a entity from ID and where if any
         *
         * @param bool|mixed $id
         *
         * @return $this
         * @throws \TFW_Exception
         */
        public function load($id = false){
            $this->_beforeLoad();

            if($id !== false)
                $this->addWhere($this->_idField, $id);

            $query = $this
                ->setLimit(0)
                ->_constructQuery();

            try{
                $rez  = $this->_db->getResults($query, false, true);

                if($rez){
                    $this->_isLoaded = true;
                    $this->_idValue  = $rez[$this->_idField];
                    $this->_data     = $rez;
                    $this->_where    = [];

                    $this->triggerEvent(self::EVENT_LOADED, $this);
                    $this->_afterLoad();
                }
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            return $this;
        }

        public function getQuery($id = false){
            if($id !== false)
                $this->addWhere($this->_idField, $id);

            $query = $this
                ->setLimit(0)
                ->_constructQuery();

           return $query;
        }

        /**
         * Loads list of entity from where
         *
         * @param bool $asModels
         * @param bool $asArray
         *
         * @return array|bool|mixed
         * @throws \TFW_Exception
         */
        public function getList($asModels = true, $asArray = false){
            $query = $this
                ->_constructQuery();

            try{
                $rez  = $this->_db->getResults($query, (!$asModels && !$asArray));
                $this->_where = [];

                if($asModels){
                    $results = [];
                    $class   = get_class($this);
                    /**
                     * @var TDB_Model $d
                     */
                    foreach($rez as $r){
                        $d = new $class();
                        if($d->loadFromArray($r))
                           $results[] = $d;
                    }

                    return $results;
                }
                else
                    return $rez;
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param array $data
         *
         * @return bool|$this|TDB_Model
         */
        public function loadFromArray($data){
            $this->_beforeLoad();

            if(isset($data[$this->_idField])){
                $this->_isLoaded = true;
                $this->_idValue  = $data[$this->_idField];
                $this->_data     = $data;
                $this->_where    = [];

                $this->triggerEvent(self::EVENT_LOADED, $this);
                $this->_afterLoad();
            }

            return $this;
        }

        /**
         * @param array $createData
         *
         * @return string
         * @throws TFW_Exception
         */
        private function _generateCreateQuery($createData){
            $query = "DESCRIBE ".$this->_table;
            try{
                $res = $this->_db->getResults($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            $query = "INSERT INTO ".$this->_table." (";
            $data  = "";

            foreach($createData as $i => $obj){
                $data  .= "(";
                foreach($res as $header){
                    if(isset($obj[$header->Field]) && $header->Field != $this->_idField){
                        if($i == 0)
                            $query .= "`$header->Field`, ";
                        $value = $obj[$header->Field];
                        //$value = self::checkValues($value, true, false);
                        $data .= "'$value', ";
                    }
                }
                $data  = substr($data, 0, strlen($data) - 2)."), ";
            }

            $query = substr($query, 0, strlen($query) - 2);
            $data  = substr($data, 0, strlen($data) - 2);

            $query .= ") VALUES ".$data;

            return $query;
        }

        /**
         * Creates an entity in DB
         *
         * @return bool
         * @throws \TFW_Exception
         */
        private function _create(){
            try{
                $query = $this->_generateCreateQuery([$this->_data]);
                $this->_db->execQuery($query);

                $this->_isLoaded = true;
                $this->_idValue  = $this->_db->getLastId();
                $this->_updated  = [];
                $this->_oldData  = [];

                $this->triggerEvent(self::EVENT_CREATED, $this);

                return true;
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param array $data
         *
         * @return bool
         * @throws TFW_Exception
         */
        public function massCreate($data){
            try{
                $query = $this->_generateCreateQuery($data);
                $this->_db->execQuery($query);
                $this->triggerEvent(self::EVENT_MASS_CREATED, $data);

                return true;
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * Save entity, if not loaded, creates it
         *
         * @return bool
         * @throws TFW_Exception
         */
        public function save(){
            if(!$this->isUpdated()){
                $this->_last_error = "Nothing to update";
                return false;
            }

            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");

            if(!$db)
                throw new TFW_Exception(get_class($this)." - Save : Database not initialised");

            $this->_beforeSave();

            if($this->_isLoaded){
                $query = "UPDATE ".$this->_table." SET ";

                foreach($this->_updated as $key){
                    $value = $this->_data[$key];
                    //$value = self::checkValues($value, true, false);
                    //$value = mysqli_real_escape_string($db->getId(), $value);
                    $query .= "`$key`='$value', ";
                }

                $query = substr($query, 0, strlen($query) - 2);
                $query .= " WHERE ".$this->_idField."=".$this->_idValue;

                try{
                    $db->execQuery($query);

                    $this->triggerEvent(self::EVENT_SAVED, [
                        "object"  => $this,
                        "oldData" => $this->_oldData
                    ]);

                    $this->_updated = [];
                    $this->_oldData = [];

                    $result = true;
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                $result = $this->_create();

            $this->_afterSave();

            return $result;
        }

        /**
         * Remove entity or entities from table
         *
         * @throws \TFW_Exception
         */
        public function delete(){
            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");

            if($db){
                if($this->isLoaded())
                    $this->addWhere($this->_idField, $this->getId());

                $query = "DELETE FROM $this->_table ".$this->_constructWhere();

                try{
                    $db->execQuery($query);

                    if($this->isLoaded())
                        $this->triggerEvent(self::EVENT_DELETED, $this);

                    $this->_isLoaded = false;
                    $this->_data     = [];
                    $this->_oldData  = [];
                    $this->_updated  = [];
                    $this->_idValue  = null;
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                throw new TFW_Exception(get_class($this)." - Unable to delete : DB not initialised");
        }

        /**
         * Used to add joints to model
         */
        protected function _addJoint(){}

        /**
         * Called before the save() event
         */
        protected function _beforeSave(){}

        /**
         * Called after the save() event
         */
        protected function _afterSave(){}

        /**
         * Called before the load() event
         */
        protected function _beforeLoad(){}

        /**
         * Called after the load() event
         */
        protected function _afterLoad(){}
    }