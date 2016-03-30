<?php

/**
 * StorageLocal class file.
 *
 * @author lx <lx@tongda2000.com>
 */
class StorageLocal extends CComponent implements IStorage {

    public function copy($source, $dest, $checkUploadable = true) {
        if ($checkUploadable && !TFileUtil::isUploadable($dest)) {
            return false;
        }
        return copy(TUtil::iconv2os($source), TUtil::iconv2os($dest));
    }

    public function remove($fileName) {
        if($this->isFileExists($fileName)) {
            @unlink(TUtil::iconv2os($fileName));
        }
    }

    public function move($oldName, $newName, $checkUploadable = true) {
        if ($checkUploadable &&!TFileUtil::isUploadable($newName)) {
            return false;
        }
        return rename(TUtil::iconv2os($oldName), TUtil::iconv2os($newName));    
    }
    public function scanDir($dirName) {
        return @scanDir($dirName);
    }
    
    public function rmdir($dirName) {
        return @rmdir($dirName);
    }

    public function mkdir($dirName, $mode=0755) {
        $dirName = TUtil::iconv2os($dirName);
        if (is_dir($dirName) || @mkdir($dirName, $mode)) 
            return true;
        if (!self::mkdir(dirname($dirName), $mode))
            return false;
        return @mkdir($dirName, $mode);
    }
    
    public function read($fileName, $length = 0) {
        if(!$this->isFileExists($fileName))
            return;
        $fileName = TUtil::iconv2os($fileName);
        $content = '';
        if($length == 0) {
            $content = file_get_contents($fileName);
        } else if(intval($length) > 0) {
            $handle = fopen($fileName, "rb");
            if($handle !== false) {
                $content = bin2hex(fread($handle, $length));
            }
            @fclose($handle);
        }
      
        return $content;
    }

    public function write($fileName, $data, $mode = NULL) {
        if (!TFileUtil::isUploadable($fileName)) {
            return false;
        }
        $fileName = TUtil::iconv2os($fileName);
        if($mode !== NULL) {
            $handle = @fopen($fileName, $mode);
            if($handle !== false) {
                $ret = fwrite($handle, $data);
                @fclose($handle);
            }
            return $ret;
        }else {
            return file_put_contents($fileName, $data);
        }
    }

    public function isFileExists($fileName) {
        return file_exists(TUtil::iconv2os($fileName));
    }
    
    public function realPath($fileName) {
        return @realpath(TUtil::iconv2os($fileName));
    }
    
    public function clearDir($dirName, $delDir = false){
        if(!is_dir($dirName)){
            if(is_file($dirName)){
                $this->remove($dirName);
            }
            return;
        }
        
        $dirs = $this->scanDir($dirName);
        foreach ($dirs as $dir){
            if($dir == '.' || $dir == '..')
                continue;
            
            $this->clearDir($dirName . DIRECTORY_SEPARATOR . $dir, true);
        }
        if($delDir)
            $this->rmdir($dirName);
        return;
    }
}

?>
