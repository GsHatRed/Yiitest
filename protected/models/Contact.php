<?php

/**
 * Contact class.
 * Contact is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class Contact extends CActiveRecord
{
	public $verifyCode;

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
		return '{{contact}}';
	}
	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			// name, email, subject and body are required
			array('name, email, subject, body', 'required'),
			// email has to be a valid email address
			array('email', 'email'),
			array('subject+email', 'uniqueMulti','message'=>'请勿重复提交'),
			// verifyCode needs to be entered correctly
			array('verifyCode', 'captcha', 'allowEmpty'=>!CCaptcha::checkRequirements()),
		);
	}
	
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
			'verifyCode'=>'驗證碼',
			'email' => '郵箱',
			'name' => '姓名',
			'subject' => '簡介',
			'body' => '內容'
		);
	}
}