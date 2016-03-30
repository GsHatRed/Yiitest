<?php

/**
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property integer $user_id
 * @property integer $a_id
 * @property string $module
 * @property string $type
 */
class Praise extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return static the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'praise';
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'user', 'user_id'),
		);
	}
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'NO.',
			'user_id' => '用户id',
			'a_id' => '被赞id',
			'module' => '被赞modle',
			'type' => '态度',
		);
	}
	public static function checkIsPraise($id,$module){
		$praise = self::model()->find('a_id=:a_id and user_id=:uid and module=:module',array(
			':a_id'=>$id,
			':uid'=>Yii::app()->user->id,
			':module'=>$module,
			));
		$return = $praise != null ? true : false;
		if(!$return){
			$praise = New Praise();
			$praise->user_id = Yii::app()->user->id;
			$praise->a_id = $id;
			$praise->module = $module;
			$praise->save();
		}
		return $return;
	} 
}
