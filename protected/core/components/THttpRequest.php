<?php

/**
 * THttpRequest class file.
 *
 * @author lx <lx@tongda2000.com>
 */
class THttpRequest extends CHttpRequest {

    /**
     * 智能文件下载
     *
     * @param string $filePath 文件路径
     * @param string $fileName 文件名
     * @param bool $checkEncrypt 是否加密验证
     * @param bool $forceDownload 是否强制下载
     * @param string $mimeType MIME类型
     * @param bool $terminate 下载完结束
     * @throws CHttpException
     */
    public function sendFile($filePath, $fileName, $checkEncrypt = true, $forceDownload = true, $mimeType = null, $terminate = true) {
        $isEncrypted = false;
        $xSendFileEnabled = true;
        $xHeader = '';

        //判断是否加密
        if ($checkEncrypt === true) {
            $encryptor = new TEncryptor($filePath);
            $fileHeader = $encryptor->getHeaderHead();
            $isEncrypted = $fileHeader['header_identifier'] == HEADER_IDENTIFIER;
            $xSendFileEnabled = $isEncrypted ? false : true;
        }

        if($isEncrypted === false) {
            if (preg_match('/apache/i', $_SERVER["SERVER_SOFTWARE"])) {
                $xSendFileEnabled = true;
                $xHeader = 'X-Sendfile';
            } else if (preg_match('/nginx/i', $_SERVER["SERVER_SOFTWARE"])) {
                $xSendFileEnabled = true;
                $xHeader = 'X-Accel-Redirect';
                $filePath = $this->getInternalPath4Nignx($filePath);
            } else if (preg_match('/microsoft-iis/i', $_SERVER["SERVER_SOFTWARE"])) {
                $xSendFileEnabled = false; //IIS暂不支持xsendfile
            } else {
                $xSendFileEnabled = false;
            }
        }

        ob_end_clean();
        if ($mimeType === null) {
            if (($mimeType = CFileHelper::getMimeTypeByExtension($fileName)) === null)
                $mimeType = 'text/plain';
        }

        if ($xSendFileEnabled) {
            $this->xSendFile($filePath, array(
                'saveName' => $this->getHttpFileName($fileName),
                'xHeader' => $xHeader,
                'mimeType' => $mimeType,
                'forceDownload' => $forceDownload
            ));
        } else {
            $content = Yii::app()->storage->read($filePath);
            $this->sendContent($fileName, $content, $forceDownload, $mimeType, $terminate);
        }
    }

    /**
     * 普通文件下载
     * 
     * @param string $fileName
     * @param string $content
     * @param bool $forceDownload
     * @param string $mimeType
     * @param bool $terminate
     * @throws CHttpException
     */
    public function sendContent($fileName, $content, $forceDownload = true, $mimeType = null, $terminate = true) {
        if($mimeType===null)
        {
            if(($mimeType=CFileHelper::getMimeTypeByExtension($fileName))===null)
                $mimeType='text/plain';
        }
        $fileSize = (function_exists('mb_strlen') ? mb_strlen($content, '8bit') : strlen($content));
        $contentStart = 0;
        $contentEnd = $fileSize - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('Accept-Ranges: bytes');

            if (strpos($_SERVER['HTTP_RANGE'], ',') !== false) {
                header("Content-Range: bytes $contentStart-$contentEnd/$fileSize");
                throw new CHttpException(416, 'Requested Range Not Satisfiable');
            }

            $range = str_replace('bytes=', '', $_SERVER['HTTP_RANGE']);

            if ($range[0] === '-')
                $contentStart = $fileSize - substr($range, 1);
            else {
                $range = explode('-', $range);
                $contentStart = $range[0];

                if ((isset($range[1]) && is_numeric($range[1])))
                    $contentEnd = $range[1];
            }

            $contentEnd = ($contentEnd > $fileSize) ? $fileSize - 1 : $contentEnd;

            // Validate the requested range and return an error if it's not correct.
            $wrongContentStart = ($contentStart > $contentEnd || $contentStart > $fileSize - 1 || $contentStart < 0);

            if ($wrongContentStart) {
                header("Content-Range: bytes $contentStart-$contentEnd/$fileSize");
                throw new CHttpException(416, 'Requested Range Not Satisfiable');
            }

            header('HTTP/1.1 206 Partial Content');
            header("Content-Range: bytes $contentStart-$contentEnd/$fileSize");
        } else
            header('HTTP/1.1 200 OK');

        $length = $contentEnd - $contentStart + 1; // Calculate new content length

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-type: $mimeType");
        header('Content-Length: ' . $length);
        if ($forceDownload)
            header("Content-Disposition: attachment; " . $this->getHttpFileName($fileName));
        else
            header("Content-Disposition: " . $this->getHttpFileName($fileName));
        header('Content-Transfer-Encoding: binary');
        $content = function_exists('mb_substr') ? mb_substr($content, $contentStart, $length) : substr($content, $contentStart, $length);

        if ($terminate) {
            // clean up the application first because the file downloading could take long time
            // which may cause timeout of some resources (such as DB connection)
            ob_start();
            Yii::app()->end(0, false);
            ob_end_clean();
            echo $content;
            exit(0);
        } else
            echo $content;
    }

    /**
     * 下载文件名兼容各浏览器
     *
     * @param string $fileName
     * @return string
     */
    public function getHttpFileName($fileName) {
        if(preg_match("/MSIE 8.0/", $_SERVER['HTTP_USER_AGENT'])) {
            $fileNameReturn = 'filename="'. str_replace("+", "%20", TUtil::iconv($fileName, Yii::app()->charset, 'gbk')) .'"';
        } else if (preg_match("/MSIE|Trident/", $_SERVER['HTTP_USER_AGENT'])) {
            $fileNameEncoded = str_replace("+", "%20", urlencode($fileName));
            $fileNameReturn = 'filename="' . $fileNameEncoded . '"';
        } else if (preg_match("/Firefox/", $_SERVER['HTTP_USER_AGENT'])) {
            if (substr($_SERVER['HTTP_USER_AGENT'], stripos($_SERVER['HTTP_USER_AGENT'], "Firefox") + 8, 3) >= "8.0")
                $fileNameReturn = 'filename="' . $fileName . '"';
            else
                $fileNameReturn = 'filename*="utf8\'\'' . $fileName . '"';
        }
        else {
            $fileNameReturn = 'filename="' . $fileName . '"';
        }

//        if (preg_match_all("/&#([0-9a-z]{4}|[0-9a-z]{6});/i", $fileNameReturn, $array)) {
//            for ($i = 0; $i < count($array[0]); $i++) {
//                $fileNameReturn = str_replace($array[0][$i], mb_convert_encoding($array[0][$i], MYOA_CHARSET, 'HTML-ENTITIES'), $fileNameReturn);
//            }
//        }
        return $fileNameReturn;
    }

    /**
     * sendfile方式下载
     *
     * @param string $filePath
     * @param array $options
     */
    public function xSendFile($filePath, $options = array()) {
        if (!isset($options['forceDownload']) || $options['forceDownload'])
            $disposition = 'attachment';
        else
            $disposition = 'inline';

        if (!isset($options['saveName']))
            $options['saveName'] = 'filename="' . basename($filePath) . '"';

        if (!isset($options['xHeader']))
            $options['xHeader'] = 'X-Sendfile';

        if ($options['mimeType'] !== null)
            header('Content-type: ' . $options['mimeType']);
        header('Content-Disposition: ' . $disposition . '; ' . $options['saveName']);
        if (isset($options['addHeaders'])) {
            foreach ($options['addHeaders'] as $header => $value)
                header($header . ': ' . $value);
        }
        header(trim($options['xHeader']) . ': ' . $filePath);

        if (!isset($options['terminate']) || $options['terminate'])
            Yii::app()->end();
    }

    /**
     * nginx开启sendfile后需要返回内部路径
     *
     * @param string $filePath
     * @return string
     */
    public function getInternalPath4Nignx($filePath) {
        preg_match('/((\/|\\\\)(attachments|resources|logs)(\/|\\\\).*)$/i', $filePath, $matches);
        return str_replace("\\", '/', $matches[0]);
    }

}
