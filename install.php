<?php
    define('ROOT_PATH', __DIR__);

    require_once "Core/autoload.php";

    TFW_Console::clrScreen();
    TFW_Console::writeLine('============= Lunching installation =============', 'yellow');

    /*
     * Configs
     */
    TFW_Console::write('Loading configs ...', 'yellow');
    try{
        TFW_Registry::loadConfigs();
        TFW_Console::writeLine('... OK', 'green');
    }
    catch(TFW_Exception $e){
        TFW_Console::writeLine(" .. Error : ".$e->getMessage(), "red");
        exit;
    }

    /*
     * Database
     */
    TFW_Console::write('Initialising database ...', 'yellow');
    try{
        $script =
            "echo \"DROP DATABASE IF EXISTS ".TFW_Registry::getConfig("database:database")."\" "
            ."| mysql -u".TFW_Registry::getConfig("database:username")." -p".TFW_Registry::getConfig("database:password").";".PHP_EOL
            ."echo \"CREATE DATABASE ".TFW_Registry::getConfig("database:database")."\" "
            ."| mysql -u".TFW_Registry::getConfig("database:username")." -p".TFW_Registry::getConfig("database:password").";";

        TFW_Console::exec($script);

        $db = new TDB_Mysql(
            TFW_Registry::getConfig("database:server"),
            TFW_Registry::getConfig("database:username"),
            TFW_Registry::getConfig("database:password"),
            TFW_Registry::getConfig("database:database"),
            TFW_Registry::getConfig("database:charset")
        );

        TFW_Registry::register("db", $db);
        TFW_Console::writeLine('... OK', 'green');
    }
    catch(TFW_Exception $e){
        TFW_Console::writeLine(" .. Error : ".$e->getMessage(), "red");
        exit;
    }

    /*
     * Module table
     */
    TFW_Console::write('Creating module table ...', 'yellow');
    try{
        $db
            ->execQuery("CREATE TABLE app_modules (
  module_key VARCHAR(150) NOT NULL,
  version VARCHAR(10) NULL,
  timestamp TEXT NULL,
  PRIMARY KEY (module_key),
  UNIQUE INDEX module_key_UNIQUE (module_key ASC));");
        TFW_Console::writeLine('... OK', 'green');
    }
    catch(TFW_Exception $e){
        TFW_Console::writeLine('... Error : '.$e->getMessage(), 'red');
        exit;
    }

    /*
     * Modules initialisation
     */

    TFW_Console::writeLine('Initialising modules ...', 'yellow');
    $modules_dir = ROOT_PATH.DIRECTORY_SEPARATOR."Module".DIRECTORY_SEPARATOR;
    try{
        foreach(TFW_IO::getDirectories($modules_dir) as $m){
            TFW_Console::write('  => '.$m.' ...', 'yellow');
            $module = new TFW_Module($m, $modules_dir.$m);
            TFW_Registry::register("module:".$module->getKey(), $module);
            TFW_Console::writeLine('... OK', 'green');
        }
    }
    catch(TFW_Exception $e){
        TFW_Console::writeLine('... Error : '.$e->getMessage(), 'red');
        exit;
    }


    /*
     * FIRST USER CREATION
     */
    TFW_Console::writeLine('');
    TFW_Console::writeLine('Do you want to create the first user ? (Y/n)', 'yellow');
    $response = TFW_Console::readLine();
    $response = strtolower($response);

    function getUserInfo(){
        TFW_Console::writeLine('First name ?', 'yellow');
        $firstname = TFW_Console::readLine();
        TFW_Console::writeLine('Last name ?', 'yellow');
        $lastname = TFW_Console::readLine();
        TFW_Console::writeLine('Email ?', 'yellow');
        $email = TFW_Console::readLine();
        TFW_Console::writeLine('Password ?', 'yellow');
        $pass = TFW_Console::readLine();
        TFW_Console::writeLine('Redo password', 'yellow');
        $passagain = TFW_Console::readLine();

        if($pass != $passagain)
            return 'Password arn\'t the same';
        else if(empty($firstname) || empty($lastname) || empty($email) || empty($pass))
            return 'Missing information';
        else{
            return array(
                $firstname,
                $lastname,
                $email,
                $pass
            );
        }
    }

    if(empty($response) || $response == 'y'){
        $info = false;

        while(!is_array($info))
            $info = getUserInfo();

        TFW_Console::writeLine('');
        TFW_Console::write('Creating user ...', 'yellow');
        try{
            $pass = md5($info[3]);
            $db->execQuery("INSERT INTO app_users (first_name, last_name, email, password, id_group) "
                ."VALUES ('$info[0]', '$info[1]', '$info[2]', '$pass', 1)");
            TFW_Console::writeLine('... OK', 'green');
        }
        catch(TFW_Exception $e){
            TFW_Console::writeLine('... Error : '.$e->getMessage(), 'red');
            exit;
        }
    }

    // Chown files
    TFW_Console::write('Reset permissions ...', 'yellow');
    TFW_Console::exec('chown -R www-data:www-data '.ROOT_PATH);
    TFW_Console::writeLine('... OK', 'green');

    /***
     * TODO : ADD TO my.cnf
     *
     * [mysqld]
    port = 3306
    sql-mode="" OR => set GLOBAL sql_mode='ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'
     */

    $db->close();

    TFW_Console::writeLine('============= Ending installation =============', 'yellow');
    TFW_Console::writeLine('');
    TFW_Console::writeLine('====================================================', 'red');
    TFW_Console::writeLine('==== Please delete this file after last install ====', 'red');
    TFW_Console::writeLine('====================================================', 'red');