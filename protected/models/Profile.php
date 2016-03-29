<?php

/**
 * The followings are the available columns in table 'tbl_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $profile
 */
class Profile extends CActiveRecord
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
		return 'profile';
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
			array('user_id', 'required'),
			array('user_id', 'unique'),
			array('name', 'length', 'max'=>128),
			array('mobile', 'match', 'pattern' => '/^0{0,1}(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])[0-9]{8}$/', 'message' => '手机号格式不对！'),
            array('qq', 'match', 'pattern' => '/^[1-9]\d[0-9]{3,10}$/', 'message' => 'QQ格式不对！'),
            array('avatar',   'file',   'allowEmpty'=>true,  'types'=>'jpg,gif,png',  'maxSize'=>1024 * 1024 * 1,  'tooLarge'=>'头像最大不超过1MB，请重新上传!',  ), 
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'NO.',
			'name' => '暱稱',
			'mobile' => '手機號',
			'qq' => 'QQ號',
			'last_visit_ip' => '最後登陸IP',
			'last_visit_time' => '最後登陸時間',
			'avatar' => '頭像',
			'online_time' => '在線時間',
			'profiles' => '簡介',
			'sex' => '性别'
		);
	}
	public static function avatarHelper($avatar){
		$file = Yii::app()->basePath.Yii::app()->params['avatarUrl'].Yii::app()->user->id.'/'.$avatar;
		if(file_exists($file))
			return Yii::app()->params['avatarView'].Yii::app()->user->id.'/'.$avatar;
		else
			echo $file;
			return Yii::app()->params['avatarView'].'0/avatar.png';
	}
	public static function avatarByUserId($id){
		$model = self::model()->find('user_id=:user_id', array(':user_id'=>$id));
		return self::avatarHelper($model->avatar);
	}
}
