<?php

/**
 * Algorithm interface file.
 *
 * @author lx <lx@tongda2000.com>
 */
interface IAlgorithm
{
    public function encrypt($data); 
    public function decrypt($data);
    public function getVersion();
}
?>
