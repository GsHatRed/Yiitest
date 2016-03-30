<?php
/**
 * Description of TSync
 *
 * @author F.L
 */

Yii::import('core.components.sync_drivers.*');

class TNotifySync extends TComponentFactory {
    public $driverPath = 'core.components.sync_drivers';
    public $driverClass = 'NotifySync';
    public $interfaceClass = 'INotifySync';
}
