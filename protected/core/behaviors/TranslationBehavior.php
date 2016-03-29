<?php
/**
 * 翻译行为类
 *
 */

/**
 * 自动给model数据增加多语言处理（保存、删除、显示）.
 * Udage：
 * Add the flowing code in your model behaviors.
 * return array(
 *      'TranslationBehavior' => array(
 *          'class' => 'core.behaviors.TranslationBehavior',
 *          'attributes' => array('xxx','xxx'),
 *          'pk' => 'id'
 *      ),
 * );
 *
 * @author lx <lx@tongda2000.com>
 * @version $Id$
 */
class TranslationBehavior extends CActiveRecordBehavior {
    
    /**
     *
     * @var array 待翻译属性
     */
    public $attributes = array();
    
    /**
     * @var str 主键
     */
    public $pk;

    /**
     * @todo 处理翻译的字段值
     */
    public function afterFind($event) {
        parent::afterFind($event);
        $language=Yii::app()->language;
        if(strtolower($language)!=='zh_cn'){
            $model=$this->owner->tableName();
            $id=$this->pk;
            $pk=$this->owner[$id];
            $attributes=$this->attributes;
            $dataArr=  Translation::model()->findAll(TUtil::qc("model").'=:model and '.TUtil::qc("pk").'=:pk',array(':model'=>$model,':pk'=>$pk));
            foreach($attributes as $key =>$value){
                foreach($dataArr as $dataKey => $dataValue){
                    if($dataValue->attribute==$value && !empty($dataValue->data)){
                        $data=  json_decode($dataValue->data);
                        if(!empty($data->$language))
                            $this->owner[$value]=$data->$language;
                    }
                }
            }
        }
    }
    
    /**
     * @todo 保存翻译的值
     */
    public function afterSave($event) {
        parent::afterSave($event);
        if($this->owner[isNewRecord]){
            $attribute=$this->attributes;
            $id=$this->pk;
            $id=$this->owner[$id];
            $tableName=$this->owner->tableName();
            foreach($attribute as $key =>$value){
                $model=new Translation();
                $model->isNewRecord=TRUE;
                $model->model=$tableName;
                $model->pk=$id;
                $model->attribute=$value;
                $model->save();
            }
        }
//        else{
//            //更新时候，判断表里边有没有数据，如果没有，则插入
//            $attribute=$this->attributes;
//            $id=$this->pk;
//            $id=$this->owner[$id];
//            $tableName=$this->owner->tableName();
//            foreach($attribute as $key =>$value){
//                $total=Translation::model()->count('model=:model and pk=:pk and attribute=:attribute',array(':model'=>$tableName,':pk'=>$id,':attribute'=>$value));
//                
//                if($total<=0){
//                    $model=new Translation();
//                    $model->isNewRecord=TRUE;
//                    $model->model=$tableName;
//                    $model->pk=$id;
//                    $model->attribute=$value;
//                    $model->save();
//                }
//            }
//            
//        }
    }
    
    /**
     * @todo 删除翻译的值
     */
    public function afterDelete($event) {
        parent::afterDelete($event);
        $id=$this->pk;
        $id=$this->owner[$id];
        $tableName=$this->owner->tableName();
        $attribute=$this->attributes;
        foreach($attribute as $key => $value){
            Translation::model()->deleteAll(TUtil::qc("model").'=:model and '.TUtil::qc("pk").'=:pk and '.TUtil::qc("attribute").'=:attribute',array(':model'=>$tableName,':pk'=>$id,':attribute'=>$value));
        }
    }   
}
