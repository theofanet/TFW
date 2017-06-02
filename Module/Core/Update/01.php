<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 13/02/2017
     */
    class Core_Update_01 extends TDB_Update{

        public function run(){
            /*
             * APP CONFIG TABLE
             */
            self::add((new TDB_Updater_Table('app_settings'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('setting_key', TDB_Updater_Column::VARCHAR))->setLength(100)->addIndex(TDB_Updater_Column::INDEX_UNIQUE))
                ->addColumn(new TDB_Updater_Column('setting_value', TDB_Updater_Column::TEXT))
            );

            /*
             * CRON TABLE
             */
            self::add((new TDB_Updater_Table('app_cron_tasks'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('code', TDB_Updater_Column::VARCHAR))->setLength(100)->addIndex(TDB_Updater_Column::INDEX_UNIQUE))
                ->addColumn((new TDB_Updater_Column('status', TDB_Updater_Column::INT))->setDefault(0))
                ->addColumn(new TDB_Updater_Column('error_message', TDB_Updater_Column::TEXT))
                ->addColumn(new TDB_Updater_Column('last_execution_time', TDB_Updater_Column::DATETIME))
                ->addColumn(new TDB_Updater_Column('next_execution_time', TDB_Updater_Column::DATETIME))
            );

            /*
             * LANGUAGES TABLE
             */
            self::add((new TDB_Updater_Table('app_languages'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('lang_code', TDB_Updater_Column::VARCHAR))->addIndex(TDB_Updater_Column::INDEX_INDEX))
                ->addColumn((new TDB_Updater_Column('lang_name', TDB_Updater_Column::VARCHAR))->setLength(150)));

            /*
             * USERS GROUPS TABLE
             */
            self::add((new TDB_Updater_Table('app_users_groups'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('name', TDB_Updater_Column::VARCHAR))->setLength(150))
                ->addColumn(new TDB_Updater_Column('rights', TDB_Updater_Column::TEXT))
            );

            /*
             * USERS TABLE
             */
            self::add((new TDB_Updater_Table('app_users'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('id_group', TDB_Updater_Column::INT))->addForeignKey("app_users_groups", "id", false))
                ->addColumn((new TDB_Updater_Column('app_language', TDB_Updater_Column::VARCHAR))->setDefault('en'))
                ->addColumn(new TDB_Updater_Column('first_name', TDB_Updater_Column::TEXT))
                ->addColumn(new TDB_Updater_Column('last_name', TDB_Updater_Column::TEXT))
                ->addColumn(new TDB_Updater_Column('email', TDB_Updater_Column::TEXT))
                ->addColumn(new TDB_Updater_Column('password', TDB_Updater_Column::TEXT))
                ->addColumn((new TDB_Updater_Column('active', TDB_Updater_Column::INT))->setDefault(1))
            );

            /*
             * USERS NOTIFICATIONS TABLE
             */
            self::add((new TDB_Updater_Table('app_users_notifications'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('id_recipient', TDB_Updater_Column::INT))->addForeignKey("app_users", 'id', true))
                ->addColumn((new TDB_Updater_Column('title', TDB_Updater_Column::VARCHAR))->setLength(200)->setDefault(""))
                ->addColumn(new TDB_Updater_Column('content', TDB_Updater_Column::TEXT))
                ->addColumn((new TDB_Updater_Column('seen', TDB_Updater_Column::INT))->setDefault(0))
                ->addColumn(new TDB_Updater_Column('created_at', TDB_Updater_Column::DATETIME))
                ->addColumn(new TDB_Updater_Column('updated_at', TDB_Updater_Column::DATETIME))
            );

            /*
             * API KEYS TABLE
             */
            self::add((new TDB_Updater_Table('app_api_keys'))
                ->addIdColumn()
                ->addColumn((new TDB_Updater_Column('active', TDB_Updater_Column::INT))->setDefault(1))
                ->addColumn((new TDB_Updater_Column('label', TDB_Updater_Column::VARCHAR))->setLength(100))
                ->addColumn((new TDB_Updater_Column('value', TDB_Updater_Column::VARCHAR))->setLength(20))
                ->addColumn((new TDB_Updater_Column('id_group', TDB_Updater_Column::INT)))
                ->addColumn((new TDB_Updater_Column('created_at', TDB_Updater_Column::DATETIME)))
            );
        }

        public function populate(){
            $this->insert("app_settings", ["setting_key", "setting_value"], [
                ["app.name", TFW_Registry::getConfig("project:name")],
                ["app.copyright", "© ".TFW_Registry::getConfig("project:company")],
                ["tables.filters.text.mode", 2]
            ]);

            $this->insert("app_languages", ["lang_code", "lang_name"], [
                ["en", "English"],
                ["fr", "Français"]
            ]);

            $this->insert("app_users_groups", ["name", "rights"], [
                ["ALL", "Kg=="],
                ["NONE", ""]
            ]);
        }

    }