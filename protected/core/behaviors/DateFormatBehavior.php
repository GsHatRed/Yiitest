<?php
/**
 * 日期时间格式转换行为类
 *
 */

/**
 * 针对数据库中的时间戳字段自动转换：
 * 1、保存时自动转换称时间戳
 * 2、find后自动根据local或者设置的格式转换
 * 
 * 在model的behaviors方法中添加如下代码：
 * return array(
 *      'DateFormatBehavior' => array(
 *          'class' => 'core.behaviors.DateFormatBehavior',
 *          'attributes' => array(
 *              'create_time'=> 'y年M月d日 H:m:s'),
 *              'end_time'=> 'y年M月d日 H:m:s'),       
 *      ),
 * ); 
 * 
 */

class DateFormatBehavior extends CActiveRecordBehavior {
    public $attributes;
    
    /**
     * @var boolean 是否启用自动填充时间
     */
    public $autoFill = true;
    
    public $emptyMark = '-';
    
    const FORMAT_DEFAULT = 'y-M-d H:m:s';
        
    public function afterFind ($event)
    {
        foreach ($this->attributes as $attribute => $format)
        {
            if(! $this->owner->hasAttribute($attribute))
                continue;
            $format = $format == null ? self::FORMAT_DEFAULT : $format;
            if($this->owner->getAttribute($attribute) == '0'){
                $this->attributes[$attribute] = $this->emptyMark;
            } else {
                $this->attributes[$attribute] = Yii::app()->dateFormatter->format($format, $this->owner->getAttribute($attribute));
            }
        }
        $this->owner->setAttributes($this->attributes);

        parent::afterFind ($event);
    }

    public function beforeValidate ($event)
    {
        $setAttributes = array();
        foreach ($this->attributes as $attribute => $format)
        {
            if(! $this->owner->hasAttribute($attribute))
                continue;

            $value = $this->owner->getAttribute($attribute);
            
            if(empty($value) && $this->autoFill === false)
                continue;
            
            if(empty($value))
                $value = time();
            else if(!is_numeric($value))
                $value = strtotime($value);
            $setAttributes[$attribute] = $value;
        }
        $this->owner->setAttributes($setAttributes);
        return parent::beforeValidate ($event);
    }
}
?>
