<?php

    /**
     * TFrameWork2
     *
     * Created by Theo
     * Date: 14/02/2017
     */
    class Core_Helper_Text extends TFW_Helper{

        /**
         * Generate a random password
         *
         * @param int $numAlpha
         * @param int $numNumber
         * @param int $numNonAlpha
         *
         * @return string
         */
        public function generatePass($numAlpha = 10, $numNumber = 10, $numNonAlpha = 0){
            $listAlpha    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $listNumber   = '0123456789';
            $listNonAlpha = ',;:!?.$/*-+&@_+;./*&?$-!,';

            return str_shuffle(
                substr(str_shuffle($listAlpha),0,$numAlpha) .
                substr(str_shuffle($listNumber),0,$numNumber) .
                substr(str_shuffle($listNonAlpha),0,$numNonAlpha)
            );
        }

        /**
         * Generate a random filename
         *
         * @param int $size
         *
         * @return string
         */
        public function generateFilename($size = 20){
            return $this->generatePass($size/4, $size/4).time().$this->generatePass($size/4, $size/4);
        }

        /**
         * Clean a string
         *
         * @param string $txt
         *
         * @return string
         */
        public function cleanText($txt){
            $txt = strtr($txt, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
            $txt = preg_replace('/([^.a-z0-9]+)/i', '_', $txt);
            $txt = str_replace(" ", "_", $txt);
            $txt = str_replace("-", "_", $txt);

            return strtolower($txt);
        }

    }