<?php

    /**
     * Created by Theo.
     */
    abstract class TDB_Update extends TFW_Abstract{
        /**
         * @param TDB_Updater_Table $table
         *
         * @return bool
         */
        public static function add(TDB_Updater_Table $table){
            return TDB_Updater_Core::add($table);
        }

        /**
         * @param TDB_Updater_Table $table
         *
         * @return bool
         */
        public static function update(TDB_Updater_Table $table){
            return TDB_Updater_Core::add($table, TDB_Updater_Core::ACTION_UPDATE);
        }

        /**
         * @param TDB_Updater_Table $table
         *
         * @return bool
         */
        public static function delete(TDB_Updater_Table $table){
            return TDB_Updater_Core::add($table, TDB_Updater_Core::ACTION_DELETE);
        }

        /**
         * Main loop^for tables construction
         */
        abstract public function run();

        /**
         * Used to populate DB after installation
         */
        public function populate(){

        }

        /**
         * @param string $table
         * @param array  $what
         * @param array  $values
         *
         * @return $this|TDB_Update
         * @throws \TFW_Exception
         */
        public function insert($table, $what, $values){
            /**
             * @var TDB_Mysql $db
             */
            if(count($values) && !is_array($values))
                $values = [$values];

            $db = TFW_Registry::getRegistered("db");
            if($db){
                try{
                    $vals = "";
                    foreach($values as $v)
                        $vals .= "('".implode("','", $v)."'), ";
                    $vals = substr($vals, 0, strlen($vals) - 2);
                    $db->execQuery("INSERT INTO ".$table." (".implode(",", $what).") VALUES ".$vals);
                }
                catch(TFW_Exception $e){
                    throw $e;
                }
            }
            else
                throw new TFW_Exception(get_class($this)." Unable to populate table ".$table." - DB not initialised");

            return $this;
        }

    }