<?php

/**
 * This is the model class for table "focus".
 *
 * The followings are the available columns in table 'focus':
 * @property integer $id
 * @property integer $f_user_id
 * @property integer $t_user_id
 * @property integer $type
 */
class Focus extends CActiveRecord
{
	private $_isFocus;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'focus';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('f_user_id, t_user_id', 'required'),
			array('f_user_id, t_user_id, type', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, f_user_id, t_user_id, type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'f_user' => array(self::BELONGS_TO, 'User', 'f_user_id'),
			't_user' => array(self::BELONGS_TO, 'User', 't_user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'f_user_id' => '粉絲',
			't_user_id' => '關注',
			'type' => '關係',
			'create_time' => '關注時間'
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('f_user_id',$this->f_user_id);
		$criteria->compare('t_user_id',$this->t_user_id);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Focus the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public static function isFocus($id, $type = '0', $change = 'yes'){
		$isFocus = self::model()->find('f_user_id=:fuid and t_user_id=:tuid',array(':fuid'=>Yii::app()->user->id,':tuid'=>$id));
		if(empty($isFocus)){
			if($change=='yes'){
				$focus = new Focus;
				$focus->f_user_id = Yii::app()->user->id;
				$focus->t_user_id = $id;
				$focus->type = $type;
				$focus->save();
			}
			return false;
		}else{
			if($type == $isFocus->type){
				return true;
			}else{
				if($change=='yes'){
					$isFocus->type = $type;
					$isFocus->save();
				}
				return false;
			}
		}
	}
	public static function isFocusEasy($id){
		$isFocus = self::model()->find('f_user_id=:fuid and t_user_id=:tuid',array(':fuid'=>Yii::app()->user->id,':tuid'=>$id));
		if(empty($isFocus)){
			$focus = new Focus;
			$focus->f_user_id = Yii::app()->user->id;
			$focus->t_user_id = $id;
			$focus->type = '0';
			$focus->save();
			return false;
		}else{
			if($isFocus->type == 0){
				$isFocus->type = 1;
				$isFocus->save();
				return true;
			}else{
				$isFocus->type = 0;
				$isFocus->save();
				return false;
			}
		}
	}
	public static function countFfocus($id){
		return self::model()->count('f_user_id=:fuid and type=:type',array(':fuid'=>$id,':type'=>0));
	}

	public static function countTfocus($id){
		return self::model()->count('t_user_id=:tuid and type=:type',array(':tuid'=>$id,':type'=>0));
	}

	public static function fansRank($id = null){
		$rank = '這還排什麼啊';
		$criteria = new CDbCriteria;
		$criteria->addCondition("type=0");
		$criteria->select = "count(*),t_user_id";
		$criteria->group = 't_user_id';
		$criteria->order = 'count(*) DESC,t_user_id ASC' ;
		$model = self::model()->findAll($criteria);
		foreach ($model as $key => $data) {
			if($data['t_user_id'] == $id)
				$rank = $key+1;
		}
		return $rank;
	}

	public function beforeSave(){
		if(parent::beforeSave()){
			$this->create_time = time();
			return true;
		}else{
			return false;
		}
	}

	public function afterFind(){
		parent::afterFind();
		$this->_isFocus = Focus::isFocus($this->t_user_id, 0, 'no');
	}
	public function getFocus()
	{
		return Focus::isFocus($this->t_user_id, 0, 'no');
	}
}
