<?php
/**
 * TStorage class file.
 *
 * @author lx <lx@tongda2000.com>
 */

Yii::import('core.components.storage_drivers.*');

class TStorage extends TComponentFactory {
    public $driverPath = 'core.components.storage_drivers';
    public $driverClass = 'StorageLocal';
    public $interfaceClass = 'IStorage';
}