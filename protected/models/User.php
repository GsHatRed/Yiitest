<?php

/**
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $profile
 */
class User extends CActiveRecord
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
		return '{{user}}';
	}
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'profile' => array(self::HAS_ONE, 'Profile', 'user_id'),
			'posts' => array(self::HAS_MANY, 'Post', 'author_id'),
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
			array('username, password', 'required'),
			array('username', 'unique'),
			array('username, password, email', 'length', 'max'=>128),
			array('email','email'),
			//array('username+email', 'uniqueMulti','message'=>'用户名和邮箱重复'),
			array('id,email,username', 'safe', 'on' => 'search'),
		);
	}


	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'NO.',
			'username' => '用戶名',
			'password' => '密碼',
			'email' => '郵箱',
			'status' => '狀態'
		);
	}

	/**
	 * Checks if the given password is correct.
	 * @param string the password to be validated
	 * @return boolean whether the password is valid
	 */
	public function validatePassword($password)
	{
		return CPasswordHelper::verifyPassword($password,$this->password);
	}

	/**
	 * Generates the password hash.
	 * @param string password
	 * @return string hash
	 */
	public function hashPassword($password)
	{
		return CPasswordHelper::hashPassword($password);
	}
	public function afterSave(){
		parent::afterSave();
		if(empty($this->profile)){
			$profile = new Profile;
			$profile->user_id = $this->id;
			$profile->save();
		}
	}
	public static function getNameById($id){
		if(empty($id)){
			return '歡迎註冊!';
		}else{
			$model = User::model()->findByPk($id);
			if(empty($model->profile->name))
				return $model->username;
			else
				return $model->profile->name;
		}
	}
	public function getUrl()
	{
		return Yii::app()->createUrl('/set/profile', array(
			'id'=>$this->id,
		));
	}
	public static function getTotal(){
		return self::model()->count();
	}
	public static function getOnlineNum(){
		return self::model()->count('status=:status',array(':status'=>1));
	}
	public static function getOnlineUser(){
		return self::model()->findAll('status=:status',array(':status'=>1));
	}
}
