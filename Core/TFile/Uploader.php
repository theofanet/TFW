<?php

    class TFile_Uploader extends TFW_Abstract {
        protected $file_ext;
        protected $directory;
        protected $max_size;
        protected $file_name;

        const ERR_CODE_FILE_EXTENSION = 'file_ext';
        const ERR_CODE_FILE_SIZE	  = 'file_size';
        const ERR_CODE_FILE_UPLOAD    = 'file_upload';


        /**
         * TFile_Uploader constructor.
         *
         * @param bool|string $dir
         */
        function __construct($dir = false){
            $this->file_ext  = array();
            $this->max_size  = 1000000000;
            $this->directory = $dir ? $dir : ROOT_PATH.TFW_IO::DS."var".TFW_IO::DS."tmp".TFW_IO::DS;
            $this->file_name = false;

            if(substr($this->directory, -1) !== TFW_IO::DS)
                $this->directory .= TFW_IO::DS;

            $this->_checkFolderExist();
        }

        /**
         * Check folder existence. Create it if necessary
         */
        protected function _checkFolderExist(){
            $paths   = explode(TFW_IO::DS, $this->directory);
            $current = '';

            foreach($paths as $p){
                $current .= $p.TFW_IO::DS;
                if(!file_exists($current))
                    mkdir($current);
            }
        }

        /**
         * @param string $ext
         *
         * @return $this
         */
        public function addExt($ext){
            if($ext[0] != ".")
                $ext = ".$ext";
            array_push($this->file_ext,  strtoupper($ext));
            return $this;
        }

        /**
         * @param array $ext
         *
         * @return $this
         */
        public function setExt($ext = array()){
            foreach($ext as $e)
                array_push($this->file_ext, strtoupper($e));

            return $this;
        }

        /**
         * @param int $m
         *
         * @return $this
         */
        public function setMaxSize($m){
            $this->max_size = $m;
            return $this;
        }

        /**
         * @param string $name
         *
         * @return $this
         */
        public function setFilename($name){
            $this->file_name = $name;
            return $this;
        }

        /**
         * @param array $file
         *
         * @return bool|string
         */
        public function proceed($file){
            $basename    = basename($file['name']);
            $size        = filesize($file['tmp_name']);
            $extension   = strtoupper(strrchr($file['name'], '.'));
            $non_up_ext  = strrchr($file['name'], '.');

            if(!empty($this->file_ext) && !in_array($extension, $this->file_ext))
                $this->_last_error = self::ERR_CODE_FILE_EXTENSION;
            else if($size > $this->max_size)
                $this->_last_error = self::ERR_CODE_FILE_SIZE;
            else {
                if($this->file_name)
                    $basename = $this->file_name.$extension;

                $basename = TFW_Registry::getHelper("Core/Text")->cleanText($basename);
                $i = 1;
                $base_name = basename($basename, $non_up_ext);
                while(file_exists($this->directory . $basename)){
                    $basename = $base_name."_".$i.$non_up_ext;
                    $i++;
                }

                if(!move_uploaded_file($file['tmp_name'], $this->directory . $basename))
                    $this->_last_error = self::ERR_CODE_FILE_UPLOAD;
            }

            if($this->_last_error != false)
                return false;
            else
                return $basename;
        }
    }