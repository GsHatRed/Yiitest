<?php
/**
 * TTokenizer class file.
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('core.components.tokenizer_drivers.*');

class TTokenizer extends TComponentFactory {
    public $driverPath = 'core.components.tokenizer_drivers';
    public $driverClass = 'TokenizerSCWS';
    public $interfaceClass = 'ITokenizer';
}
?>
