<?php

/**
 * 安全行为类
 *
 */
class SecurityBehavior extends CActiveRecordBehavior {

    public $attributes = array();
    protected $purifier;

    public function __construct() {
        $this->purifier = new CHtmlPurifier();
        $this->purifier->options = array(
            'URI.AllowedSchemes' => array(
                'http' => true,
                'https' => true,
            ),
        );
    }

    public function beforeSave($event) {
        foreach ($this->attributes as $k=>$v) {
            if(is_array($v)) {
                $this->purifier->options = array_merge($this->purifier->options, $v);
            } else {
                $attribute = $v;
            }
            $this->owner->{$attribute} = $this->purifier->purify($this->owner->{$attribute});
        }
    }

}
