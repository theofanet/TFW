<?php

    /**
     * Created by Theo.
     */
    class TDB_Updater_Table extends TFW_Abstract{
        protected $_name;

        /**
         * @var TDB_Mysql $_db
         */
        protected $_db;

        protected $_engine = 'InnoDB';

        protected $_column;
        protected $_add_after;

        protected $_dependencies;

        /**
         * TDB_Updater_Table constructor.
         *
         * @param string $name
         *
         * @return $this|TDB_Updater_Table
         */
        public function __construct($name){
            $this->_name = $name;
            $this->_db   = TFW_Registry::getRegistered("db");

            $this->_add_after = false;

            $this->_dependencies = array();

            return $this;
        }

        /**
         * @return string
         */
        public function getName(){
            return $this->_name;
        }

        /**
         * @return array
         */
        public function getDependencies(){
            return $this->_dependencies;
        }

        /**
         * @param string $key
         * @param string $type
         *
         * @return $this|TDB_Updater_Table
         */
        public function addIdColumn($key = 'id', $type = TDB_Updater_Column::INT){
            return $this->addColumn((new TDB_Updater_Column($key, $type))->setAutoIncrement()->addIndex(TDB_Updater_Column::INDEX_INDEX));
        }

        /**
         * @param TDB_Updater_Column $column
         * @param bool               $after
         *
         * @return $this
         */
        public function addColumn(TDB_Updater_Column $column, $after = false){
            $this->_column[] = $column;

            if($column->getDependency())
                $this->_dependencies[] = $column->getDependency();

            if($after)
                $this->_add_after = $after;

            return $this;
        }

        /**
         * @return array|mysqli_result
         * @throws TFW_Exception
         */
        public function remove(){
            $query = "DROP TABLE IF EXISTS ".$this->_name;

            try{
                return $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @return array|mysqli_result
         * @throws TFW_Exception
         */
        public function clear(){
            $query = "TRUNCATE TABLE ".$this->_name;

            try{
                return $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @return array|mysqli_result
         * @throws TFW_Exception
         */
        public function create(){
            $indexes = array(
                TDB_Updater_Column::INDEX_PRIMARY => array(),
                TDB_Updater_Column::INDEX_UNIQUE  => array(),
                TDB_Updater_Column::INDEX_INDEX   => array()
            );

            $foreignKeys = array();

            $query = 'CREATE TABLE '.$this->_name.'(';

            /**
             * @var TDB_Updater_Column $c
             */
            foreach($this->_column as $c){
                $query .= $c->get().', ';
                $index  = $c->getIndex();

                if($index){
                    foreach($index as $i)
                        $indexes[$i][] = $c->getName();
                }

                $fk = $c->getForeignKey();
                if($fk)
                    $foreignKeys[] = $fk;
            }

            if(count($indexes[TDB_Updater_Column::INDEX_PRIMARY]))
                $query .= 'PRIMARY KEY ('.implode(',', $indexes[TDB_Updater_Column::INDEX_PRIMARY]).'), ';
            if(count($indexes[TDB_Updater_Column::INDEX_UNIQUE]))
                $query .= 'UNIQUE KEY ('.implode(',', $indexes[TDB_Updater_Column::INDEX_UNIQUE]).'), ';

            foreach($foreignKeys as $fk)
                $query .= $fk.', ';

            $query = substr($query, 0, strlen($query) - 2);
            $query .= ') ENGINE='.$this->_engine.' DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;';

            $result = null;
            try{
                $result = $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }

            return $result;
        }

        /**
         * @return array|mysqli_result
         * @throws TFW_Exception
         */
        public function update(){
            $after = $this->_add_after;

            $indexes = array(
                TDB_Updater_Column::INDEX_PRIMARY => array(),
                TDB_Updater_Column::INDEX_UNIQUE  => array(),
                TDB_Updater_Column::INDEX_INDEX   => array()
            );

            $foreignKeys = array();

            $query = 'ALTER TABLE '.$this->_name.' ';

            /**
             * @var TDB_Updater_Column $c
             */
            foreach($this->_column as $c){
                $query  .= 'ADD '.$c->get().($after ? ' AFTER '.$after : '').', ';
                $after   = $c->getName();
                $index   = $c->getIndex();

                if($index){
                    foreach($index as $i)
                        $indexes[$i][] = $c->getName();
                }

                $fk = $c->getForeignKey();
                if($fk)
                    $foreignKeys[] = implode(', ADD ', explode(',', $fk));
            }

            if(count($indexes[TDB_Updater_Column::INDEX_PRIMARY]))
                $query .= 'ADD PRIMARY ('.implode(',', $indexes[TDB_Updater_Column::INDEX_PRIMARY]).'), ';
            if(count($indexes[TDB_Updater_Column::INDEX_UNIQUE]))
                $query .= 'ADD UNIQUE ('.implode(',', $indexes[TDB_Updater_Column::INDEX_UNIQUE]).'), ';
            if(count($indexes[TDB_Updater_Column::INDEX_INDEX]))
                $query .= 'ADD INDEX ('.implode(',', $indexes[TDB_Updater_Column::INDEX_INDEX]).'), ';
            foreach($foreignKeys as $fk)
                $query .= 'ADD '.$fk.', ';

            $query = substr($query, 0, strlen($query) - 2).';';

            try{
                return $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

        /**
         * @param string $new_name
         *
         * @return array|mysqli_result
         * @throws TFW_Exception
         */
        public function rename($new_name){
            $query = 'RENAME TABLE '.$this->_name.' TO '.$new_name;

            try{
                return $this->_db->execQuery($query);
            }
            catch(TFW_Exception $e){
                throw $e;
            }
        }

    }