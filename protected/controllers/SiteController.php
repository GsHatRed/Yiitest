<?php

class SiteController extends Controller
{
	public $layout='column1';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model = new Contact;
		$model->unsetAttributes();
		
		if(isset($_POST['Contact']))
		{
			if(!Yii::app()->user->isGuest){
				$model->name = User::getNameById(Yii::app()->user->id);
				$model->email = Yii::app()->user->email;
			}
			$model->attributes=$_POST['Contact'];
			if($model->validate() && $model->save())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','感谢您的意见,我会尽快回复!');
				$this->refresh();
			}else{
				//var_dump($model->errors);
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		$model = new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		User::model()->updateByPk(Yii::app()->user->id,array('status'=>'0')); 
		$profile = Profile::model()->find('user_id=:uid',array(':uid'=>Yii::app()->user->id));
		$time = time() - $profile->last_visit_time;
		$omin = floor($time / 60);
		$osec = $time % 60;
		$otime = $osec>50 ? $omin+1 : $omin;
		$profile->online_time = $otime;
		$profile->save();
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}
	public function actionRegist(){
		$model = new User();
		if(isset($_POST['ajax']) && $_POST['ajax']==='regist-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		if(isset($_POST['User']))
		{
			$_POST['LoginForm'] = $_POST['User'];
			$model->attributes = $_POST['User'];
			$model->password = crypt($_POST['User']['password'],Yii::app()->params['password_encrypt_salt']);
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->save()){
				$models = new LoginForm;
				$models->attributes = $_POST['LoginForm'];
				$models->rememberMe = 0;
				if($models->validate() && $models->login()){
					$this->redirect(Yii::app()->user->returnUrl);
				}
			}
		}
		// display the login form
		$this->render('regist',array('model'=>$model));
	}
}
