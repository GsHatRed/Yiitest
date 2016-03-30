<?php
/**
 * Created by PhpStorm.
 * User: lx
 * Date: 15/6/25
 * Time: 下午4:56
 */

class TAssetManager extends CAssetManager{
    /**
     * Generates path segments relative to basePath.
     * @param string $file for which public path will be created.
     * @param bool $hashByName whether the published directory should be named as the hashed basename.
     * @return string path segments without basePath.
     * @since 1.1.13
     */
    protected function generatePath($file,$hashByName=false)
    {
        if (is_file($file))
            $pathForHashing = dirname($file);
        else
            $pathForHashing = $file;

        //发布的目录名取相对路径，保障一致性
        $pathForHashing = str_replace("\\", '/', $pathForHashing);
        $pos = stripos($pathForHashing, '/framework/');
        if($pos == false) {
            $pos = stripos($pathForHashing, '/protected/');
        }
        $pathForHashing = substr($pathForHashing, $pos);
        return $this->hash($pathForHashing);
    }
}