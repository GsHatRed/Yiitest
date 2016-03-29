<?php

/*
 * Tag标签行为类
 */

Yii::import('core.models.Tags');

class TagBehavior extends CActiveRecordBehavior {
    
    public $attribute = 'tags';
    
    public function afterSave($event) {
        $attribute = $this->attribute;
        if($this->owner->$attribute){
            $model = $this->getModel();
            if($model) {
                $model->tags = $this->owner->$attribute;
                $model->save();
            } else {
                $model = new Tags();
                $model->attributes = array(
                    'model' => $this->owner->tableName(),
                    'pk' => $this->owner->id,
                    'tags' => $this->owner->$attribute,
                );
                $model->save();
            }
        }
    }
    
    public function afterDelete($event) {
        $model = $this->getModel();
        if($model)
            $model->delete();
    }
    
//    public function afterFind($event) {
//        $attribute = $this->attribute;
//        $this->owner->$attribute = $this->getModel()->tags;
//    }
    
    public function getModel(){
        return Tags::model()->find(TUtil::qc("model").'=:model AND '.TUtil::qc("pk").'=:pk', array(
                ':model'=>$this->owner->tableName(), 
                ':pk' => $this->owner->id));
    }
    
    public function getTagCloud() {
        return Tags::model()->findAllByAttributes(array('model'=>Yii::app()->controller->module->id));
    }
    
    public function getTagCriteria($tag, $criteria = NULL){
        if($criteria == NULL)
            $criteria = new CDbCriteria();
        
        $criteriaChild = new CDbCriteria();
        $criteriaChild->join = "LEFT JOIN tags ON tags.pk = t.id AND tags.model = '{$this->owner->tableName()}'".
            " LEFT JOIN tags_item ON tags_item.tags_id = tags.id";
        $criteriaChild->addCondition(TUtil::qc('tags_item.tag_name')." = :tag");
        $criteriaChild->params = array(
            ':tag' => $tag
        );
        
        $criteria->mergeWith($criteriaChild);
        return $criteria;
    }
}
?>
