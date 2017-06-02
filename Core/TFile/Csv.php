<?php

    class TFile_Csv extends TFile_Base{
        protected $_lineLength;
        protected $_delimiter;
        protected $_enclosure;

        protected $_data;

        protected $_valid_extensions = array('csv', 'log');

        /**
         * TFile_Csv constructor.
         *
         * @param bool $path
         */
        public function __construct($path = false){
            parent::__construct($path);

            $this->_lineLength = 0;
            $this->_delimiter  = ';';
            $this->_enclosure  = '"';

            $this->_data = array();

            return $this;
        }

        /**
         * @param int $length
         *
         * @return $this
         */
        public function setLineLength($length){
            $this->_lineLength = $length;
            return $this;
        }

        /**
         * @param string $delimiter
         *
         * @return $this
         */
        public function setDelimiter($delimiter){
            $this->_delimiter = $delimiter;
            return $this;
        }

        /**
         * @param string $enclosure
         *
         * @return $this
         */
        public function setEnclosure($enclosure){
            $this->_enclosure = $enclosure;
            return $this;
        }

        /**
         * @return array
         */
        public function getData(){
            ini_set('auto_detect_line_endings', true);

            $this->_data = array();

            if(!file_exists($this->_path))
                return array($this->_path);

            if(!$this->_file || $this->_opened_mode != self::OPEN_READ)
                $this->open();

            while ($rowData = fgetcsv($this->_file, $this->_lineLength, $this->_delimiter, $this->_enclosure))
                $this->_data[] = $rowData;

            $this->close();

            return $this->_data;
        }

        /**
         * @param array $data
         */
        public function setData(Array $data){
            $this->_data = $data;
            $this->save();
        }

        /**
         * Save file with actual DATA
         */
        private function save(){
            if(!$this->_file || $this->_opened_mode != self::OPEN_WRITE)
                $this->open(self::OPEN_WRITE);

            foreach ($this->_data as $dataRow)
                $this->fputcsv($dataRow, $this->_delimiter, $this->_enclosure);

            $this->close();
            $this->_after_save();
        }

        /**
         * @param array  $fields
         * @param string $delimiter
         * @param string $enclosure
         *
         * @return int
         */
        private function fputcsv($fields, $delimiter, $enclosure) {
            $str         = '';
            $escape_char = '\\';

            foreach($fields as $value) {
                if(strpos($value, $delimiter) !== false ||
                    strpos($value, $enclosure) !== false ||
                    strpos($value, "\n") !== false ||
                    strpos($value, "\r") !== false ||
                    strpos($value, "\t") !== false ||
                    strpos($value, ' ') !== false) {

                    $str2    = $enclosure;
                    $escaped = 0;
                    $len     = strlen($value);

                    for($i = 0; $i < $len; $i++){
                        if($value[$i] == $escape_char)
                            $escaped = 1;
                        else if(!$escaped && $value[$i] == $enclosure)
                            $str2 .= $enclosure;
                        else
                            $escaped = 0;

                        $str2 .= $value[$i];
                    }

                    $str2 .= $enclosure;
                    $str .= $str2.$delimiter;
                } else
                    $str .= $enclosure.$value.$enclosure.$delimiter;
            }

            $str  = substr($str, 0, -1);
            $str .= "\n";

            return fwrite($this->_file, $str);
        }

    }