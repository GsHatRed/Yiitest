<?php
/**
 * TMobileSMS class file.
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('core.components.sms_drivers.*');

class TMobileSMS extends TComponentFactory {
    public $driverPath = 'core.components.sms_drivers';
    public $driverClass = 'SMSGetway';
    public $interfaceClass = 'ISMS';
}