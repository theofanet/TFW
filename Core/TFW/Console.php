<?php

    class TFW_Console extends TFW_Abstract{

        private static $_foreground_colors = array(
            'black'        => '0;30',
            'dark_gray'    => '1;30',
            'blue'         => '0;34',
            'light_blue'   => '1;34',
            'green'        => '0;32',
            'light_green'  => '1;32',
            'cyan'         => '0;36',
            'light_cyan'   => '1;36',
            'red'          => '0;31',
            'light_red'    => '1;31',
            'purple'       => '0;35',
            'light_purple' => '1;35',
            'brown'        => '0;33',
            'yellow'       => '1;33',
            'light_gray'   => '0;37',
            'white'        => '1;37'
        );

        private static $_background_colors = array(
            'black'      => '40',
            'red'        => '41',
            'green'      => '42',
            'yellow'     => '43',
            'blue'       => '44',
            'magenta'    => '45',
            'cyan'       => '46',
            'light_gray' => '47'
        );

        public static function write($string, $textColor = false, $backgroundColor = false){
            $colored_string = "";

            if(isset(self::$_foreground_colors[$textColor]))
                $colored_string .= "\033[" . self::$_foreground_colors[$textColor] . "m";

            if(isset(self::$_background_colors[$backgroundColor]))
                $colored_string .= "\033[" . self::$_background_colors[$backgroundColor] . "m";

            $colored_string .=  $string .($textColor || $backgroundColor ? "\033[0m" : "" );

            fwrite(STDOUT, $colored_string);
        }

        public static function writeLine($string = "", $textColor = false, $backgroundColor = false){
            self::write($string, $textColor, $backgroundColor);
            echo PHP_EOL;
        }

        public static function dumpVar($var, $textColor = false, $backgroundColor = false){
            ob_start();
            print_r($var);
            $text = ob_get_contents();
            ob_end_clean();

            self::writeLine($text, $textColor, $backgroundColor);
        }

        public static function readLine(){
            return function_exists('readline') ? readline() : self::_readline();
        }

        protected static function _readline($prompt = null){
            if($prompt)
                TFW_Console::write($prompt);

            $fp = fopen("php://stdin","r");
            $line = rtrim(fgets($fp, 1024));

            return $line;
        }

        public static function clrScreen(){
            self::exec('clear');
        }

        public static function exec($cmd, $sys = true){
            if($sys)
                return system($cmd);
            else
                return exec($cmd);
        }
    }