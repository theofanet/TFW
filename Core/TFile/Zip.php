<?php

    class TFile_Zip extends TFile_Base{
        protected $_opened;
        protected $_removeAfterClose;

        protected $_valid_extensions = array('zip');

        protected $_errors = array(
            ZipArchive::ER_EXISTS => 'File does not exists',
            ZipArchive::ER_INCONS => 'Weak zip file',
            ZipArchive::ER_INVAL  => 'Invalid arguments',
            ZipArchive::ER_MEMORY => 'Malloc failure',
            ZipArchive::ER_NOENT  => 'File does not exists',
            ZipArchive::ER_NOZIP  => 'File isn\'t a ZIP',
            ZipArchive::ER_OPEN   => 'Unable to open file',
            ZipArchive::ER_READ   => 'Unable to read file',
            ZipArchive::ER_SEEK   => 'Seek error'
        );


        /**
         * TFile_Zip constructor.
         *
         * @param bool|string $path
         */
        public function __construct($path = false){
            parent::__construct($path);

            $this->_file = new ZipArchive();
            $this->_removeAfterClose = array();
        }

        /**
         * @param bool $create
         *
         * @return $this
         * @throws \TFW_Exception
         */
        public function open($create = true){
            if($create || !$this->_exists)
                $opened = $this->_file->open($this->_path, ZipArchive::CREATE);
            else
                $opened = $this->_file->open($this->_path);

            if($opened !== true)
                throw new TFW_Exception('Unable to open zip file '.$this->fileName()." : ".$this->_errors[$opened]);

            $this->_opened = true;

            return $this;
        }

        /**
         * @param string $content
         */
        public function addContent($content){
            $parts = explode(TFW_IO::DS, $this->_path);
            array_pop($parts);
            $this->_checkFolderExist(implode(TFW_IO::DS, $parts));
            file_put_contents($this->filePath(), $content);

            if(!$this->_exists){
                $this->_path_infos = pathinfo($this->_path);
                $this->_exists     = true;
            }
        }

        /**
         * @return $this
         * @throws \TFW_Exception
         */
        public function close(){
            if($this->_opened){
                $this->_file->close();
                $this->_opened = false;

                foreach($this->_removeAfterClose as $filename){
                    if(file_exists($filename))
                        unlink($filename);
                }

                $this->_removeAfterClose = array();

                if(!$this->_exists && file_exists($this->_path)){
                    $this->_path_infos = pathinfo($this->_path);
                    $this->_exists     = true;
                }

                return $this;
            }
            else
                throw new TFW_Exception('Unable to close '.$this->fileName().'because it\'s not open');
        }

        /**
         * @param \TFile_Base $file
         * @param bool        $remove_after
         *
         * @return $this
         * @throws \TFW_Exception
         */
        public function addFile(TFile_Base $file, $remove_after = false){
            if($this->_opened){
                if(file_exists($file->filePath())) {
                    $this->_file->addFile($file->filePath(), $file->fileName());

                    if($remove_after)
                        $this->_removeAfterClose[] = $file->filePath();

                    return $this;
                }
                else
                    throw new TFW_Exception('Unable to add '.$file->fileName()." to zip - File not found");
            }
            else
                throw new TFW_Exception('Unable to add file to ZIP because it hasn\'t been opened');
        }

        /**
         * @param string $destination
         *
         * @return bool
         */
        public function extractTo($destination){
            if($this->_exists){
                $this->open(false);
                $this->_checkFolderExist($destination);
                $done = $this->_file->extractTo($destination);
                $this->close();
                return $done;
            }

            return false;
        }

        /**
         * @param array $filename_list
         * @param bool  $remove_after
         *
         * @return $this
         * @throws \TFW_Exception
         */
        public function addFiles(Array $filename_list, $remove_after = false){
            if(is_array($filename_list)){
                foreach($filename_list as $filename){
                    $file = NULL;

                    if(is_string($filename))
                        $file = new TFile_Base($filename);
                    else if($filename instanceof TFile_Base)
                        $file = $filename;

                    try{
                        if($file)
                            $this->addFile($file, $remove_after);
                    }
                    catch(TFW_Exception $e){
                        throw $e;
                    }
                }
            }

            return $this;
        }
    }