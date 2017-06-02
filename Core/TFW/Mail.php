<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 11/04/2017
     */
    class TFW_Mail extends TFW_Abstract{
        private $_charset    = "utf-8";
        private $_priority   = 3;
        private $_mimeVers   = "1.0";
        private $_replyTo    = "";
        private $_returnPath = "";
        private $_company    = "";

        private $_subject = "";
        private $_body    = [];
        private $_files   = [];

        private $_from = "";
        private $_to   = [];
        private $_cc   = [];
        private $_bcc  = [];


        const BREAK_LINE = "\n";
        const CRLF       = "\r\n";

        /**
         * TFW_Mail constructor.
         */
        public function __construct(){
            $domain = rtrim(str_replace("http://", "", TFW_Registry::getConfig("project:url")), "/");

            $this->_replyTo    = "no-reply@$domain";
            $this->_from       = TFW_Registry::getConfig("project:name")." <$this->_replyTo>";
            $this->_returnPath = "www-data@$domain";
            $this->_company    = TFW_Registry::getConfig("project:company");
        }

        /**
         * @param string $sStr
         *
         * @return mixed
         */
        private function quoted_printable_encode($sStr) {
            return str_replace("%", "=", rawurlencode($sStr));
        }

        /**
         * @param string $address
         *
         * @return string|bool
         */
        public static function checkMail($address){
            return filter_var($address, FILTER_VALIDATE_EMAIL);
        }

        /**
         * @param string $str
         *
         * @return string
         */
        public function fixEOL($str){
            // Normalise to \n
            $nStr = str_replace(array(self::CRLF, "\r"), "\n", $str);
            // Now convert LE as needed
            if(self::BREAK_LINE !== "\n")
                $nStr = str_replace("\n", self::BREAK_LINE, $nStr);
            return $nStr;
        }

        /**
         * @param string $string
         * @param int    $line_max
         *
         * @return string
         */
        public function encodeQP($string, $line_max = 76){
            if(function_exists('quoted_printable_encode'))
                return $this->fixEOL(quoted_printable_encode($string));

            // Fall back to a pure PHP implementation
            $string = str_replace(
                array('%20', '%0D%0A.', '%0D%0A', '%'),
                array(' ', self::CRLF."=2E", self::CRLF, '='),
                rawurlencode($string)
            );

            $string = preg_replace('/[^'.self::CRLF.']{' . ($line_max - 3) . '}[^='.self::CRLF.']{2}/', "$0=".self::CRLF, $string);

            return $this->fixEOL($string);
        }

        /**
         * @param string $str
         * @param string $encoding
         *
         * @return string
         */
        public function encodeString($str, $encoding = 'base64'){
            $encoded = '';
            
            switch (strtolower($encoding)) {
                case 'base64':
                    $encoded = chunk_split(base64_encode($str), 76, self::BREAK_LINE);
                    break;
                case '7bit':
                case '8bit':
                    $encoded = $this->fixEOL($str);
                    // Make sure it ends with a line break
                    if(substr($encoded, -(strlen(self::BREAK_LINE))) != self::BREAK_LINE)
                        $encoded .= self::BREAK_LINE;
                    break;
                case 'binary':
                    $encoded = $str;
                    break;
                case 'quoted-printable':
                    $encoded = $this->encodeQP($str);
                    break;
                default:
                    break;
            }
            
            return $encoded;
        }

        /**
         * @return string
         */
        private function createHeaders() {
            $sHeaders  = 'X-Priority: '.$this->_priority.self::BREAK_LINE;
            $sHeaders .= 'Organisation: '.$this->_company.self::BREAK_LINE;
            $sHeaders .= 'Date: '.date("D, j M Y H:i:s +0200").self::BREAK_LINE;
            $sHeaders .= 'MIME-version: '.$this->_mimeVers.self::BREAK_LINE;
            $sHeaders .= 'From: '.$this->_from.self::BREAK_LINE;
            $sHeaders .= 'Reply-To: '.$this->_replyTo.self::BREAK_LINE;
            $sHeaders .= 'Return-Path: '.$this->_returnPath.self::BREAK_LINE;
            $sHeaders .= 'Sender: '.$this->_from.self::BREAK_LINE;
            $sHeaders .= 'X-Sender: '.$this->_from.self::BREAK_LINE;

            if(count($this->_cc) > 0)
                $sHeaders .= 'Cc:'.implode(',', $this->_cc).self::BREAK_LINE;
            if(count($this->_bcc) > 0)
                $sHeaders .= 'Bcc:'.implode(',', $this->_bcc).self::BREAK_LINE;

            return $sHeaders;
        }

        /**
         * @param array       $aElement
         * @param string|null $sBoundary
         *
         * @return string
         */
        private function createSection($aElement, $sBoundary = null) {
            $sMessage = '';

            if($sBoundary)
                $sMessage = self::BREAK_LINE.self::BREAK_LINE.'--_Part_'.$sBoundary.self::BREAK_LINE;

            $sMessage .= 'Content-Type: '.$aElement['ContentType'];

            if(!empty($aElement['Charset']))
                $sMessage .= '; charset="'.$aElement['Charset'].'"';
            else if($aElement['ContentTransferEncoding'] == 'base64')
                $sMessage .= '; name="'.$aElement['Filename'].'"';

            $sMessage .= self::BREAK_LINE;
            $sMessage .= 'Content-Transfer-Encoding: '.$aElement['ContentTransferEncoding'].self::BREAK_LINE;

            if(!empty($aElement['Filename']))
                $sMessage .= '; filename="'.$aElement['Filename'].'"';

            $sMessage .= self::BREAK_LINE;

            if(!empty ($aElement['Content-ID']))
                $sMessage .= 'Content-ID: <'.$aElement['Content-ID'].'>'.self::BREAK_LINE;

            $sMessage .= self::BREAK_LINE;

            if(isset($aElement["Content"]))
                $sMessage .= $aElement['Content'].self::BREAK_LINE.self::BREAK_LINE;

            $sMessage .= "--".self::BREAK_LINE.self::BREAK_LINE;

            return $sMessage;
        }

        /**
         * @param string $sub
         *
         * @return $this
         */
        public function setSubject($sub){
            $this->_subject = $sub;
            return $this;
        }

        /**
         * @param string $from
         *
         * @return $this
         */
        public function setFrom($from){
            $this->_from = $from;
            return $this;
        }

        /**
         * @param string $to
         *
         * @return $this
         */
        public function addTo($to){
            $toList = explode(',', str_replace(', ', ',', $to));

            foreach($toList as $t){
                if(self::checkMail($t))
                    $this->_to[] = $t;
            }

            return $this;
        }

        /**
         * @param string $cc
         *
         * @return $this
         */
        public function addCc($cc){
            $ccList = explode(',', str_replace(', ', ',', $cc));

            foreach($ccList as $t){
                if(self::checkMail($t))
                    $this->_cc[] = $t;
            }

            return $this;
        }

        /**
         * @param string $bcc
         *
         * @return $this
         */
        public function addBcc($bcc){
            $bccList = explode(',', str_replace(', ', ',', $bcc));

            foreach($bccList as $t){
                if(self::checkMail($t))
                    $this->_bcc[] = $t;
            }

            return $this;
        }

        /**
         * @param string $charset
         *
         * @return $this
         */
        public function setCharset($charset){
            $this->_charset = $charset;
            return $this;
        }

        /**
         * @param int $priority
         *
         * @return $this
         */
        public function setXPriority($priority = 3){
            if($priority > 5)
                $priority = 5;
            elseif($priority < 1)
                $priority = 1;

            $this->_priority = $priority;

            return $this;
        }

        /**
         * @param string $rPath
         *
         * @return $this
         */
        public function setReturnPath($rPath){
            if($this->checkMail($rPath))
                $this->_returnPath = $rPath;
            return $this;
        }

        /**
         * @param string $reply_to
         *
         * @return $this
         */
        public function setReplyTo($reply_to){
            if($this->checkMail($reply_to))
                $this->_replyTo = $reply_to;
            return $this;
        }

        /**
         * @param \TFile_Base $file
         *
         * @return $this
         */
        public function addAttachment(TFile_Base $file) {
            $this->_files[] = array(
                'ContentType'             => "application/octet-stream",
                'ContentTransferEncoding' => 'base64',
                'ContentDisposition'      => 'attachment',
                'Filename'                => $file->fileName(),
                'Content'                 => $this->encodeString($file->readAttachment())
            );

            return $this;
        }

        /**
         * @param string      $sBody
         * @param string      $sType
         * @param string|bool $sCharset
         *
         * @return $this
         */
        public function setBody($sBody, $sType = 'text/html', $sCharset = false) {
            $this->_body['ContentType']             = $sType;
            $this->_body['Charset']                 = $sCharset ? $sCharset : $this->_charset;
            $this->_body['ContentTransferEncoding'] = 'base64';
            $this->_body['ContentDisposition']      = 'inline';
            $this->_body['Content']                 = $this->encodeString($sBody);

            return $this;
        }

        /**
         * @return bool
         */
        public function send() {
            $sHeaders = $this->createHeaders();

            //if($this->template)
            //    $this->set_body($this->template->get());

            $boundary = md5(rand());

            $sHeaders .= 'Content-Type: multipart/mixed;'.self::BREAK_LINE;
            $sHeaders .= '    boundary="_Part_'.$boundary.'"'.self::BREAK_LINE;

            $mMessage = $this->createSection($this->_body, $boundary);
            $mMessage .= self::BREAK_LINE.self::BREAK_LINE;

            foreach($this->_files as $fData) {
                $mMessage .= $this->createSection($fData, $boundary);
                $mMessage .= self::BREAK_LINE.self::BREAK_LINE;
            }

            $sSubject = '=?'.$this->_charset.'?q?'.$this->quoted_printable_encode($this->_subject).'?=';

            if(@mail(implode(', ', $this->_to), $sSubject, $mMessage, $sHeaders) === false)
                return false;

            return true;
        }

        /**
         * @param string $key
         * @param array  $vars
         *
         * @throws \TFW_Exception
         */
        public function setTemplate($key, $vars = []){
            $parts  = explode("/", $key);
            $module = array_shift($parts);
            $file   = implode(TFW_IO::DS, $parts);

            $filePath = "Module".TFW_IO::DS
                .$module.TFW_IO::DS
                ."Assets".TFW_IO::DS
                ."template".TFW_IO::DS
                .$file;

            if(file_exists(ROOT_PATH.TFW_IO::DS.$filePath))
                $template_content = file_get_contents(ROOT_PATH.TFW_IO::DS.$filePath);
            else
                throw new TFW_Exception("Unable to find template file ".$key." - ".ROOT_PATH.TFW_IO::DS.$filePath);

            $template_content = str_replace(array_keys($vars), array_values($vars), $template_content);

            $this->setBody($template_content);
        }
    }