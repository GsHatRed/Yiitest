<?php
/**
 * Storage interface file.
 *
 * @author lx <lx@tongda2000.com>
 */
interface IStorage {
    public function move($oldName, $newName);
    public function copy($source, $dest);
    public function remove($fileName);
    public function scanDir($dirName);
    public function mkdir($dirName);
    public function rmdir($dirName);
    public function read($fileName);
    public function write($fileName, $content);
    public function isFileExists($fileName);
    public function realPath($fileName);
    public function clearDir($dirName, $delDir);
}
?>
