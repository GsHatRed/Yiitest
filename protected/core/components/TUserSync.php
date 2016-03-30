<?php
/**
 * Description of TSync
 *
 * @author F.L
 */

Yii::import('core.components.sync_drivers.*');

class TUserSync extends TComponentFactory {
    public $driverPath = 'core.components.sync_drivers';
    public $driverClass = 'UserSync';
    public $interfaceClass = 'IUserSync';
}
