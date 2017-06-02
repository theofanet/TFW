\<?php

    /**
     * Created by Theo.
     */

    class TDB_Updater_Column extends TFW_Abstract{
        /*
         * COLUMNS TYPE
         */
        const INT      = 'int(%d)';
        const VARCHAR  = 'varchar(%d)';
        const TEXT     = 'text';
        const DATE     = 'date';
        const DATETIME = 'datetime';
        const DOUBLE   = 'double';
        const FLOAT    = 'float';

        /*
         * COLUMNS INDEXES
         */
        const INDEX_PRIMARY  = 'PRIMARY';
        const INDEX_UNIQUE   = 'UNIQUE';
        const INDEX_INDEX    = 'INDEX';
        const INDEX_FULLTEXT = 'FULLTEXT';

        /*
         * DATA
         */
        protected $_name;
        protected $_type;
        protected $_length;
        protected $_default;
        protected $_attribut;
        protected $_null;
        protected $_index;
        protected $_auto_increment;
        protected $_primary;
        protected $_foreignKey;
        protected $_dependency;

        /**
         * TDB_Updater_Column constructor.
         *
         * @param string $name
         * @param        $type
         * @param bool   $primary
         */
        public function __construct($name, $type, $primary = false){
            $this->_name           = $name;
            $this->_type           = $type;
            $this->_length         = 11;
            $this->_default        = false;
            $this->_attribut       = false;
            $this->_null           = false;
            $this->_index          = array();
            $this->_auto_increment = false;
            $this->_primary        = $primary;
            $this->_foreignKey     = false;
            $this->_dependency     = false;

            switch($type){
                case self::INT:
                case self::DOUBLE:
                case self::FLOAT:
                    $this->_default = 0;
                    break;
                case self::VARCHAR:
                    $this->_default = "''";
                    break;
                case self::DATETIME:
                case self::DATE:
                    $this->_default = "CURRENT_TIMESTAMP";
                    break;
            }

            return $this;
        }

        /**
         * @param int $len
         *
         * @return $this
         */
        public function setLength($len){
            $this->_length = $len;
            return $this;
        }

        /**
         * @return string
         */
        public function get(){
            if($this->_index || $this->_foreignKey)
                $this->_default = false;

            $type   = @vsprintf($this->_type, [$this->_length]);

            $result = '`'.$this->_name.'` '
                .$type
                .($this->_attribut ? ' '.$this->_attribut : '').' '
                .($this->_null ? '' : 'NOT NULL')
                .($this->_default !== false ? ' DEFAULT '.$this->_default : '')
                .($this->_auto_increment ? ' AUTO_INCREMENT' : '');

            return $result;
        }

        /**
         * @param bool $auto
         *
         * @return $this
         */
        public function setAutoIncrement($auto = true){
            if($auto && !in_array(self::INDEX_PRIMARY, $this->_index))
                $this->addIndex(self::INDEX_PRIMARY);

            $this->_auto_increment = $auto;
            return $this;
        }

        /**
         * @param mixed $default
         *
         * @return $this
         */
        public function setDefault($default){
            switch($this->_type){
                case self::VARCHAR:
                    $default = "'$default'";
                    break;
            }
            $this->_default = $default;
            return $this;
        }

        /**
         * @return string
         */
        public function getName(){
            return $this->_name;
        }

        /**
         * @return array|bool
         */
        public function getIndex(){
            if(count($this->_index))
                return $this->_index;

            return false;
        }

        /**
         * @param string $index
         *
         * @return $this
         */
        public function addIndex($index){
            $this->_index[] = $index;
            return $this;
        }

        /**
         * @param string $reference_table
         * @param string $reference_key
         * @param bool   $on_delete_cascade
         *
         * @return $this
         */
        public function addForeignKey($reference_table, $reference_key = 'id', $on_delete_cascade = false){
            $uniqid = uniqid();

            $this->_foreignKey = 'KEY `fk_'.$uniqid.'_'.$this->_name.'` (`'.$this->_name.'`),'
                .'CONSTRAINT `fk_'.$uniqid.'_'.$this->_name.'` FOREIGN KEY (`'.$this->_name.'`) REFERENCES `'.$reference_table.'` (`'.$reference_key.'`)'
                .($on_delete_cascade ? ' ON DELETE CASCADE' : '');

            $this->_dependency = $reference_table;

            return $this;
        }

        /**
         * @return bool|string
         */
        public function getForeignKey(){
            return $this->_foreignKey;
        }

        /**
         * @return bool|string
         */
        public function getDependency(){
            return $this->_dependency;
        }

    }