<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
	public $email;
	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			//array('email', 'email'),
			// password needs to be authenticated
			array('password', 'authenticate'),
			array('username, password, rememberMe', 'safe'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe' => '記住我',
			'username' => '用戶名',
			'password' => '密碼',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$param)
	{
		$this->_identity=new UserIdentity($this->username,$this->password);
		if(!$this->_identity->authenticate())
			$this->addError('password','密碼不正確.');
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			$user = $this->_identity->user;
            Yii::app()->user->setIsAdmin(in_array($user->username, Yii::app()->authManager->admins));		
			$profile = $user->profile;
			$profile->last_visit_ip = Yii::app()->request->userHostAddress;
			$profile->last_visit_time = time();
			$profile->save();
			$user->status = 1;
			$user->save();
			$this->_identity->setState('username', $user->username);
            $this->_identity->setState('id', $user->id);
            $this->_identity->setState('email', $user->email);
            $this->_identity->setState('qq', $user->profile->qq);
            $this->_identity->setState('name', $user->profile->name);
            $this->_identity->setState('profiles', $user->profile->profiles);
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
