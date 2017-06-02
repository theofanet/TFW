<?php

    class TFile_Base extends TFW_Abstract{
        protected $_path;
        protected $_path_infos;
        protected $_file;
        protected $_opened_mode;

        protected $_exists;

        protected $_valid_extensions = array();

        const INFO_FILENAME = 'basename';
        const INFO_FILEDIR  = 'dirname';
        const INFO_FILEEXT  = 'extension';

        const OPEN_READ   = 'r';
        const OPEN_WRITE  = 'w';
        const OPEN_APPEND = 'a';


        /**
         * TFile_Base constructor.
         *
         * @param bool|string $path
         */
        public function __construct($path = false){
            if($path === false)
                $path = ROOT_PATH.TFW_IO::DS."var".TFW_IO::DS."tmp".TFW_IO::DS.TFW_Registry::getHelper('Core/Text')->generateFilename();

            $this->_path = $this->_checkFilename($path);

            if(file_exists($this->_path)) {
                $this->_path_infos = pathinfo($this->_path);
                $this->_exists     = true;
            }
            else
                $this->_exists = false;

            $this->_file        = NULL;
            $this->_opened_mode = NULL;
        }

        /**
         * @param bool $without_extension
         *
         * @return bool|mixed|string
         */
        public function fileName($without_extension = false){
            if(!$without_extension)
                return $this->_get_fileinfo(self::INFO_FILENAME);
            else
                return str_replace('.'.$this->_get_fileinfo(self::INFO_FILEEXT), '', $this->_get_fileinfo(self::INFO_FILENAME));
        }

        /**
         * @return bool|string
         */
        public function fileDir(){
            return $this->_get_fileinfo(self::INFO_FILEDIR);
        }

        /**
         * @return bool|string
         */
        public function fileExt(){
            return $this->_get_fileinfo(self::INFO_FILEEXT);
        }

        /**
         * @param bool $readable
         *
         * @return int|string
         */
        public function fileSize($readable = false){
            if(file_exists($this->_path))
                return $readable ? $this->_readableSize(filesize($this->_path)) : filesize($this->_path);

            return 0;
        }

        /**
         * @return string
         */
        public function filePath(){
            return $this->_path;
        }

        /**
         * @return mixed
         */
        public function filePathNoRoot(){
            return str_replace(ROOT_PATH, '', $this->filePath());
        }

        /**
         * @return bool
         */
        public function fileExists(){
            return $this->_exists;
        }

        /**
         * Update info after save
         */
        protected function _after_save(){
            if(file_exists($this->_path))
                $this->_path_infos  = pathinfo($this->_path);
        }

        /**
         * @param string $mode
         */
        public function open($mode = self::OPEN_READ){
            if($this->_file)
                $this->close();

            $this->_file = fopen($this->_path, $mode);
            $this->_opened_mode = $mode;
        }

        /**
         * Close file pointer
         */
        public function close(){
            if($this->_file)
                fclose($this->_file);
            $this->_file = NULL;
        }

        /**
         * Read file content
         */
        public function readfile(){
            if(file_exists($this->_path))
                readfile($this->_path);
        }

        /**
         * @return string
         */
        public function readAttachment(){
            $file_size = $this->fileSize();
            $this->open();
            $content = fread($this->_file, $file_size);
            $this->close();
            return $content;
        }

        /**
         * Delete file
         */
        public function remove(){
            if(file_exists($this->_path))
                unlink($this->_path);
            else
                TFW_Flash::addError("Enable to remove file $this->_path");
        }

        /**
         * @param string $newPath
         *
         * @return $this
         */
        public function moveTo($newPath){
            $parts    = explode(TFW_IO::DS, $newPath);
            $fileName = array_pop($parts);
            $folder   = implode(TFW_IO::DS, $parts);

            if(file_exists($newPath))
                $newPath = $folder.'/'.date('d_m_Y_H_i').'.'.uniqid().'.'.$fileName;

            if(!file_exists($newPath)) {
                $this->_checkFolderExist($folder);

                if(rename($this->_path, $newPath)){
                    $this->_path = $newPath;
                    $this->_after_save();
                }
            }

            return $this;
        }

        /**
         * @return string
         */
        public function getContent(){
            return nl2br(file_get_contents($this->filePath()));
        }

        /**
         * @param string $content
         */
        public function addContent($content){
            if(!$this->_file)
                $this->open(self::OPEN_APPEND);
            fwrite($this->_file, $content);
            $this->close();
        }

        /**
         * @param bool        $remove_after
         * @param bool|string $file_name
         */
        public function download($remove_after = false, $file_name = false){
            if(!$file_name)
                $file_name = $this->fileName();

            header('Content-type: application/'.$this->fileExt());
            header('Content-Disposition: attachment; filename="'.$file_name.'"');
            header("Content-length: " . $this->fileSize());
            header("Pragma: no-cache");
            header("Expires: 0");

            ob_clean();
            flush();

            $this->readfile();

            if($remove_after)
                $this->remove();

            exit;
        }

        /**
         * @param string $info
         *
         * @return bool|string
         */
        private function _get_fileinfo($info){
            if(isset($this->_path_infos[$info]))
                return $this->_path_infos[$info];

            return false;
        }

        /**
         * @param string $filename
         *
         * @return string
         */
        private function _checkFilename($filename){
            $filename = htmlentities($filename, ENT_NOQUOTES, TFW_Registry::getConfig('project:charset'));

            $filename = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $filename);
            $filename = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $filename);
            $filename = preg_replace('#&[^;]+;#', '', $filename);

            $filename_exp = explode('.', $filename);
            if(count($this->_valid_extensions) && !in_array($filename_exp[count($filename_exp) - 1], $this->_valid_extensions))
                $filename_exp[count($filename_exp)] = count($this->_valid_extensions) ? $this->_valid_extensions[0] : '';

            str_replace(' ', '_', $filename_exp[count($filename_exp) - 2]);

            return implode('.', $filename_exp);
        }

        /**
         * @param string $size
         *
         * @return string
         */
        private function _readableSize($size){
            if ($size >= 1073741824)
                $size = round($size / 1073741824 * 100) / 100 . " Go";
            elseif ($size >= 1048576)
                $size = round($size / 1048576 * 100) / 100 . " Mo";
            elseif ($size >= 1024)
                $size = round($size / 1024 * 100) / 100 . " Ko";
            elseif($size > 0)
                $size = $size . " o";
            else
                $size = "-";

            return $size;
        }

        /**
         * @param string $folder
         */
        protected function _checkFolderExist($folder){
            $paths   = explode(TFW_IO::DS, $folder);
            $current = '';

            foreach($paths as $p){
                $current .= $p.TFW_IO::DS;
                if(!file_exists($current))
                    mkdir($current);
            }
        }

    }