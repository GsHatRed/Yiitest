<?php
/**
 * Description of TSync
 *
 * @author F.L
 */

Yii::import('core.components.sync_drivers.*');

class TOrgSync extends TComponentFactory {
    public $driverPath = 'core.components.sync_drivers';
    public $driverClass = 'OrgSync';
    public $interfaceClass = 'IOrgSync';
}
