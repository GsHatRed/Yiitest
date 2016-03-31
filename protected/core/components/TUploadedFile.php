<?php

/**
 * CUploadedFile类是对文件上传的封装类.
 *
 * 调用 {@link getInstances} 获取上传的所有文件实例,
 * 调用 {@link saveAs} 保存文件.
 * 可以查看文件的一下属性 {@link name},
 * {@link tempName}, {@link type}, {@link size} and {@link error}.
 *
 * @property string $name 上传文件名称.
 * @property string $tempName Web服务器保存的临时文件名称,在请求生命周期结束后自动删除.
 * @property string $type 上传文件的MIME类型，如"image/gif".
 * 此MIME类型并没有经过服务器的检查，切勿想当然使用.
 * 相反，应该使用 {@link CFileHelper::getMimeType} 获取文件真是的MIME类型.
 * @property integer $size 上传文件大小（字节）.
 * @property integer $error 错误代码.
 * @property integer $errorDesc 错误描述.
 * @property boolean $hasError 是否发生错误.
 * @property string $extensionName 上传文件扩展名.
 *
 * @package application.core
 */
class TUploadedFile extends CComponent {

    static private $_files;
    private $_name;
    private $_tempName;
    private $_type;
    private $_size;
    private $_error;
    private $_errorDesc;

    /**
     * Constructor.
     * Use {@link getInstance} to get an instance of an uploaded file.
     * @param string $name the original name of the file being uploaded
     * @param string $tempName the path of the uploaded file on the server.
     * @param string $type the MIME-type of the uploaded file (such as "image/gif").
     * @param integer $size the actual size of the uploaded file in bytes
     * @param integer $error the error code
     */
    public function __construct($name, $tempName, $type, $size, $error) {
        $this->_name = $name;
        $this->_tempName = $tempName;
        $this->_type = $type;
        $this->_size = $size;
        $this->_error = $error;
    }

    /**
     * String output.
     * This is PHP magic method that returns string representation of an object.
     * The implementation here returns the uploaded file's name.
     * @return string the string representation of the object
     */
    public function __toString() {
        return $this->_name;
    }

    /**
     * Returns all uploaded files for the given model attribute.
     * @param CModel $model the model instance
     * @param string $attribute the attribute name. For tabular file uploading, this can be in the format of "[$i]attributeName", where $i stands for an integer index.
     * @return array array of TUploadedFile objects.
     * Empty array is returned if no available file was found for the given attribute.
     */
    public static function getInstances($model, $attribute) {
        self::$_files = array();
        if (!isset($_FILES) || !is_array($_FILES))
            return [];

        foreach ($_FILES as $class => $info)
            self::collectFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
        
        $name = CHtml::resolveName($model, $attribute);
        $len = strlen($name);
        $results = array();
        //Yii::log(var_export(self::$_files, true), CLogger::LEVEL_INFO);
        foreach (array_keys(self::$_files) as $key)
            //兼容移动客户端上传的附件uploadedfile_x的格式
            if ((0 === strncmp($key, $name, $len) || 1 === preg_match('/^uploadedfile_\d+$/i', $key)) && self::$_files[$key]->getError() != UPLOAD_ERR_NO_FILE)
                $results[] = self::$_files[$key];

        return $results;
    }

        // 上传校讯图片使用
    public static function getMottoInstances($model, $attribute) {
        self::$_files = array();
        if (!isset($_FILES) || !is_array($_FILES))
            return [];

        foreach ($_FILES as $class => $info)
            self::collectFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
        
        $name = CHtml::resolveName($model, $attribute);
        $len = strlen($name);
        $results = array();
        //Yii::log(var_export(self::$_files, true), CLogger::LEVEL_INFO);
        foreach (array_keys(self::$_files) as $key)
            // var_dump($len);exit;
            //兼容移动客户端上传的附件uploadedfile_x的格式

            // if ((0 === strncmp($key, $name, $len) || 1 === preg_match('/^uploadedfile_\d+$/i', $key)) && self::$_files[$key]->getError() != UPLOAD_ERR_NO_FILE)
                $results[] = self::$_files[$key];

        return $results;
    }

    /**
     * Processes incoming files for {@link getInstanceByName}.
     * @param string $key key for identifiing uploaded file: class name and subarray indexes
     * @param mixed $names file names provided by PHP
     * @param mixed $tmp_names temporary file names provided by PHP
     * @param mixed $types filetypes provided by PHP
     * @param mixed $sizes file sizes provided by PHP
     * @param mixed $errors uploading issues provided by PHP
     */
    protected static function collectFilesRecursive($key, $names, $tmp_names, $types, $sizes, $errors) {
        if (is_array($names)) {
            foreach ($names as $item => $name)
                self::collectFilesRecursive($key . '[' . $item . ']', $names[$item], $tmp_names[$item], $types[$item], $sizes[$item], $errors[$item]);
        }
        else
            self::$_files[$key] = new TUploadedFile($names, $tmp_names, $types, $sizes, $errors);
    }

    /**
     * Saves the uploaded file.
     * @param string $file the file path used to save the uploaded file
     * @param boolean $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return boolean true whether the file is saved successfully
     */
    public function saveAs($file, $deleteTempFile = true) {
        if ($this->_error == UPLOAD_ERR_OK) {
            if (strpos("/", $this->_name) || strpos("\\", $this->_name) ||
                    strpos("'", $this->_name) || strpos("\"", $this->_name) ||
                    strpos(":", $this->_name) || strpos("*", $this->_name) ||
                    strpos("?", $this->_name) || strpos("<", $this->_name) ||
                    strpos(">", $this->_name) || strpos("|", $this->_name)) {
                $this->_errorDesc = sprintf("文件名[%s]包含[/\\'\":*?<>|]等非法字符", $this->_name);
            } else if (!TFileUtil::isUploadable($this->_name)) {
                $this->_errorDesc = sprintf("禁止上传后缀名为[%s]的文件", CFileHelper::getExtension($this->_name));
            } else if ($this->_size == 0) {
                $this->_errorDesc = sprintf("文件[%s]大小为0字节", $this->_name);
            }

            if ($deleteTempFile) {
                if (!Yii::app()->storage->move($this->_tempName, $file, false))
                    $this->_errorDesc = '上传文件失败';
            } else if (is_uploaded_file($this->_tempName)) {
                if (!Yii::app()->storage->copy($this->_tempName, $file))
                    $this->_errorDesc = '上传文件失败';
            }
        } else if ($this->_error == UPLOAD_ERR_INI_SIZE) {
            $this->_errorDesc = sprintf("文件[%s]的大小超过了系统限制（%s）", $this->_name, ini_get('upload_max_filesize'));
        } else if ($this->_error == UPLOAD_ERR_FORM_SIZE) {
            $this->_errorDesc = sprintf("文件[%s]的大小超过了表单限制", $this->_name);
        } else if ($this->_error == UPLOAD_ERR_PARTIAL) {
            $this->_errorDesc = sprintf("文件[%s]上传不完整", $this->_name);
        } else if ($this->_error == UPLOAD_ERR_NO_TMP_DIR) {
            $this->_errorDesc = sprintf("文件[%s]上传失败：找不到临时文件夹", $this->_name);
        } else if ($this->_error == UPLOAD_ERR_CANT_WRITE) {
            $this->_errorDesc = sprintf("文件[%s]写入失败", $this->_name);
        } else {
            $this->_errorDesc = sprintf("未知错误[代码：%s]", $this->_error);
        }

        if ($this->_errorDesc != '')
            return $this->_errorDesc;
        else
            return true;
    }
    
    /**
     * 
     * @return string 返回上传文件MD5散列值
     */
    public function getHash() {
        return md5_file($this->getTempName());
    }

    /**
     * @return string the original name of the file being uploaded
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * @return string the path of the uploaded file on the server.
     * Note, this is a temporary file which will be automatically deleted by PHP
     * after the current request is processed.
     */
    public function getTempName() {
        return $this->_tempName;
    }

    /**
     * @return string the MIME-type of the uploaded file (such as "image/gif").
     * Since this MIME type is not checked on the server side, do not take this value for granted.
     * Instead, use {@link CFileHelper::getMimeType} to determine the exact MIME type.
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * @return integer the actual size of the uploaded file in bytes
     */
    public function getSize() {
        return $this->_size;
    }

    /**
     * Returns an error code describing the status of this file uploading.
     * @return integer the error code
     * @see http://www.php.net/manual/en/features.file-upload.errors.php
     */
    public function getError() {
        return $this->_error;
    }
    
    /**
     * 返回文件上传错误的描述
     * @return string 错误描述
     */
    public function getErrorDesc() {
        return $this->_errorDesc;
    }

    /**
     * @return boolean whether there is an error with the uploaded file.
     * Check {@link error} for detailed error code information.
     */
    public function getHasError() {
        return $this->_error != UPLOAD_ERR_OK;
    }

}