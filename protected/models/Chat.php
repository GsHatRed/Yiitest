<?php
/**
 * The followings are the available columns in table 'chat':
 * @property integer $id
 * @property string $name
 * @property integer $frequency
 */
class Chat extends CActiveRecord
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
		return 'chat';
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
			'chatCount' => array(self::STAT, 'praise', 'a_id', 'condition'=>'type=0'),
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
			array('date', 'checkattr'),
		);
	}
	/**
     * 校验发言间隔
     */
    public function checkattr($attribute, $params) {
    	$model = self::model()->find(array(
				  'order' => 'date DESC',
				  'condition' => 'user_id='.$this->user_id,
				));
        $lastdate = $model->date;
        if ($this->date - $lastdate < 5) {
            $this->addError($attribute, '磨叽个什么?');
        }
        
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'NO.',
			'user_id' => 'By',
			'praise' => '贊',
			'parent_id' => '上層',
			'content' => '內容',
			'date' => '發佈時間'
		);
	}
	public function praiseCount() {
        self::model()->updateByPk($this->id, array('praise' => $this->praise + 1));
    }

    public static function getUserById($id){
    	return User::getNameById(self::model()->findByPk($id)->user_id);
    }
}