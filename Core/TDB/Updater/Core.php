<?php

    /**
     * Created by Theo.
     */
    class TDB_Updater_Core extends TFW_Abstract{

        const ACTION_CREATE = 'create';
        const ACTION_UPDATE = 'update';
        const ACTION_DELETE = 'remove';

        static private $_tables = array();
        static private $_waiting_dependencies = array();

        /**
         * @param TDB_Updater_Table $table
         * @param string            $action
         *
         * @return bool
         */
        public static function add(TDB_Updater_Table $table, $action = self::ACTION_CREATE){
            if(!isset(self::$_tables))
                return false;

            self::$_tables[$table->getName()] = array(
                'table'  => $table,
                'score'  => 0,
                'action' => $action
            );

            foreach($table->getDependencies() as $dep){
                if(isset(self::$_tables[$dep]))
                    self::$_tables[$dep]['score']++;
                else
                    self::$_waiting_dependencies[] = $dep;
            }

            $new_waiting = array();

            foreach(self::$_waiting_dependencies as $dep){
                if(isset(self::$_tables[$dep]))
                    self::$_tables[$dep]['score']++;
                else
                    $new_waiting[] = $dep;
            }

            self::$_waiting_dependencies = $new_waiting;

            return true;
        }

        /**
         * @param TDB_Updater_Table $table
         *
         * @return bool
         */
        public static function update(TDB_Updater_Table $table){
            return self::add($table, self::ACTION_UPDATE);
        }

        /**
         * @param TDB_Updater_Table $table
         *
         * @return bool
         */
        public static function delete(TDB_Updater_Table $table){
            return self::add($table, self::ACTION_DELETE);
        }

        /**
         * @throws TFW_Exception
         */
        public static function lunch(){
            /*
             * TABLES CREATION
             * 1. ORDER TABLES
             * 2. CREATE/UPDATE TABLES
             */

            /**
             * @var TDB_Mysql $db
             */
            $db = TFW_Registry::getRegistered("db");

            if($db){
                try{
                    $db->execQuery('SET FOREIGN_KEY_CHECKS=0;');
                }
                catch(TFW_Exception $e){
                    throw $e;
                }

                uasort(self::$_tables, array('TDB_Updater_Core', 'order_tables'));

                foreach(self::$_tables as $table) {
                    $action = $table['action'];
                    if(method_exists($table['table'], $action)){
                        try{
                            $table['table']->$action();
                        }
                        catch(TFW_Exception $e){
                            throw $e;
                        }
                    }
                    else
                        throw new TFW_Exception($table["table"]->getName().' - action not found - '.$action);
                }

                try{
                    $db->execQuery('SET FOREIGN_KEY_CHECKS=1;');
                }
                catch(TFW_Exception $e){
                    throw $e;
                }

                self::clear();
            }
            else
                throw new TFW_Exception("Database not initialised");
        }

        /**
         * @param array $a
         * @param array $b
         *
         * @return int
         */
        private static function order_tables($a, $b){
            if($a['score'] == $b['score']){
                if(in_array($a['table']->getName(), $b['table']->getDependencies()))
                    return -1;
                else if(in_array($b['table']->getName(), $a['table']->getDependencies()))
                    return 1;

                return 0;
            }

            return ($a['score'] > $b['score']) ? -1 : 1;
        }

        /**
         * Clear the tables data
         */
        private static function clear(){
            self::$_tables = array();
            self::$_waiting_dependencies = array();
        }

    }