<?php

class SetController extends Controller
{
	public $layout='column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', // allow authenticated users to access all actions
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionUpdate()
	{
		$model = $this->loadModel();
		$profile_model = $model->profile;
		if(isset($_POST['ajax']) && $_POST['ajax']==='profile-form')
		{
			echo CActiveForm::validate($model);
			echo CActiveForm::validate($profile_model);
			Yii::app()->end();
		}
		if(isset($_POST['Profile']))
		{
			$model->attributes=$_POST['User'];
			$profile_model->attributes=$_POST['Profile'];

			$image = CUploadedFile::getInstance($profile_model, 'avatar');  
			if( is_object($image) && get_class($image) === 'CUploadedFile' ){  
			    $profile_model->avatar = md5($image->name).'.'.explode('image/', $image->type)[1];  
			}else{  
			    $profile_model->avatar = 'NoPic.jpg';  
			}  

			if($model->save() && $profile_model->save())
				if(is_object($image) && get_class($image) === 'CUploadedFile'){  
			        $image->saveAs(Yii::app()->basePath.Yii::app()->params['avatarUrl'].Yii::app()->user->id.'/'.$profile_model->avatar);  
			    }
			    Yii::app()->user->setState('username', $model->username!=null ? $model->username:"");
			    Yii::app()->user->setState('email', $model->email!=null ? $model->email:"");
			    Yii::app()->user->setState('qq', $profile_model->qq!=null ? $profile_model->qq:"");
			    Yii::app()->user->setState('name', $profile_model->name!=null ? $profile_model->name:"");
			    Yii::app()->user->setState('profiles', $profile_model->profiles!=null ? $profile_model->profiles:"");
				$this->redirect(array('update'));
		}

		$this->render('update',array(
			'model'=>$model,
			'profile_model' => $profile_model
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionProfile($id = null)
	{
		$id = empty($id) ? (empty(Yii::app()->user->id) ? '1' : Yii::app()->user->id) : (int)$id;
		$visible = $id==Yii::app()->user->id ? true : false;
		$criteria = new CDbCriteria(array(
			'condition'=>'t.id="'.$id.'"',
			'with'=>'profile',
		));
		$model = $this->loadModel($id);
		$dataProvider = new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['postsPerPage'],
			),
			'criteria'=>$criteria,
		));
		$this->render('profile',array(
			'model' => $model,
			'dataProvider' => $dataProvider,
			'visible' => $visible
		));
	}



	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($id)
	{
		if($this->_model===null)
		{
			if(!Yii::app()->user->isGuest)
				$this->_model=User::model()->findbyPk($id);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
