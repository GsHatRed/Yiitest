<?php

/**
 * This is the model class for table "data_log".
 *
 * The followings are the available columns in table 'data_log':
 * @property integer $id
 * @property integer $user_id
 * @property integer $log_time
 * @property string $model
 * @property integer $pk
 * @property string $log_type
 * @property string $extra_data
 */
class DataLog extends CActiveRecord {

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return AppLog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'data_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, model, pk, log_type, user_name', 'required'),
            array('id, user_id, log_time, pk', 'numerical', 'integerOnly' => true),
            array('model, log_type, user_name', 'length', 'max' => 64),
            array('extra_data', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, user_id, log_time, model, pk, log_type, extra_data, user_name', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors() {
        return array(
            'DateFormatBehavior' => array(
                'class' => 'core.behaviors.DateFormatBehavior',
                'attributes' => array(
                    'log_time' => 'y-M-d H:m:s',
                ),
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => '用户id',
            'log_time' => '操作时间',
            'model' => '模型名称',
            'pk' => '模型主键',
            'log_type' => '日志类型',
            'extra_data' => '操作内容',
            'user_name' => '操作用户'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        $criteria = $this->searchCondition();
        $criteria->order = TUtil::qc("log_time")." desc";
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function searchCondition($group = false) {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->compare(TUtil::qc('id'), $this->id);
        $criteria->compare(TUtil::qc('user_id'), $this->user_id);
        $criteria->compare(TUtil::qc('log_time'), $this->log_time);
        $criteria->compare(TUtil::qc('model'), $this->model);
        $criteria->compare(TUtil::qc('pk'), $this->pk);
        $criteria->compare(TUtil::qc('log_type'), $this->log_type);
        $criteria->compare(TUtil::qc('extra_data'), $this->extra_data, true);
        $criteria->compare(TUtil::qc('user_name'), $this->user_name, true);
        $criteria->order = TUtil::qc('log_time')." desc";
        if($group) {
            $criteria->group = 'user_id';
        }
        return $criteria;
    }
}
