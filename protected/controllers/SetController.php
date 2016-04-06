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
			//'accessControl', // perform access control for CRUD operations
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
			array('allow',  // allow all users to access 'index' and 'view' actions.
				'actions'=>array('profile'),
				'users'=>array('*'),
			),
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
		$model = $this->loadModel(Yii::app()->user->id);
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
			    //$profile_model->avatar = 'NoPic.jpg';  
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
	 * profile.
	 */
	public function actionProfile($id = null, $type = null)
	{
		$id = empty($id) ? (empty(Yii::app()->user->id) ? '1' : Yii::app()->user->id) : (int)$id;

		$visible = $id==Yii::app()->user->id ? true : false;
		$model = $this->loadModel($id);

		$criteria = new CDbCriteria(array(
			'condition'=>'t.id="'.$id.'"',
			'with'=>'profile',
		));
		$dataProvider = new CActiveDataProvider('User', array(
			'criteria'=>$criteria,
		));

		$carticle = new CDbCriteria(array(
			'condition'=>'author_id="'.$id.'" and status="2"',
		));
		$article = new CActiveDataProvider('Post', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['postsPerPage'],
			),
			'criteria'=>$carticle,
		));

		$ctfocus = new CDbCriteria(array(
			'condition'=>'f_user_id="'.$id.'" and type="0"',
			'order'=>'create_time DESC',
		));
		$tfocus = new CActiveDataProvider('Focus', array(
			'pagination'=>array(
				'pageSize'=>empty($type) ? Yii::app()->params['focus'] : Yii::app()->params['fansPage'],
			),
			'criteria'=>$ctfocus,
		));
		$cffocus = new CDbCriteria(array(
			'condition'=>'t_user_id="'.$id.'" and type="0"',
			'order'=>'create_time DESC',
		));
		$ffocus = new CActiveDataProvider('Focus', array(
			'pagination'=>array(
				'pageSize'=>empty($type) ? Yii::app()->params['focus'] : Yii::app()->params['fansPage'],
			),
			'criteria'=>$cffocus,
		));
		$isFocus = Focus::isFocus($id, 0, 'no');
		if(empty($type)){
			$this->render('profile',array(
				'model' => $model,
				'dataProvider' => $dataProvider,
				'visible' => $visible,
				'article' => $article,
				'isFocus' => $isFocus,
				'ffocus' => $ffocus,
				'tfocus' => $tfocus
			));
		}else if($type == 'fans'){
			$this->render('_fans',array(
				'model' => $ffocus,
				'user' => $model,
				'typefocus' => 'countTfocus',
				'type' => '粉絲',
				'ffocus' => $ffocus,
				'tfocus' => $tfocus,
				'isFocus' => $isFocus,
			));
		}else{
			$this->render('_fans',array(
				'model' => $tfocus,
				'user' => $model,
				'typefocus' => 'countFfocus',
				'type' => '關注',
				'ffocus' => $ffocus,
				'tfocus' => $tfocus,
				'isFocus' => $isFocus,
			));
		}
	}
	/**
	 * 关注
	 */
	public function actionFocus(){
		if(empty(Yii::app()->user->id)){
			echo '請先登錄';
		}else if(!is_numeric($_POST['id']) && !is_numeric($_POST['type'])){
			echo '鬧毛線?';
		}else{
			if(Focus::isFocus($_POST['id'], $_POST['type'])){
				echo '請勿重複點擊,稍後再試!';
			}else{
				echo 'ok';
			}
		}
	}

	public function actionFocusEasy($id){
		if(empty(Yii::app()->user->id)){
			echo '請先登錄';
		}else if(!is_numeric($id)){
			echo '鬧毛線?';
		}else{
			if(Focus::isFocusEasy($id)){
				echo 'delete';
			}else{
				echo 'create';
			}
		}
	}

	public function actionInfo($id){
		$model = $this->loadModel($id);
		$isFocus = Focus::isFocus($id,0,'no');
		$html = '<div class="media media-user"><div class="media-left">';
		$html .= '<a href="'.$model->url.'">';
		$html .= '<img src="'.Profile::avatarByUserId($id).'" alt="'.$model->username.'"/></a></div>';
		$html .= '<div class="media-body"><h2 class="media-heading"><span class="fa fa-mars"></span> ';
		$html .= '<a href="'.$model->url.'">'.User::getNameById($model->id).'</a>';
		$html .= '<div class="time">最后登录：' . date('Y-m-d H:i:s',$model->profile->last_visit_time).'<br>';
		$html .= '粉丝:' . Focus::countTfocus($id) . ' (排名:' . Focus::fansRank($id) . ')</div></div>';
		$html .= '<div class="media-footer"><a class="btn btn-xs '.($isFocus ? "btn-danger":"btn-success").' btn-follow" href="'.Yii::app()->createUrl('/set/FocusEasy/',array('id'=>$id)).'">';
		$html .= '<span class="glyphicon glyphicon-plus"></span>'.($isFocus ? "已關注":"關注").'</a></div>';
		echo $html;
	}
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel($id)
	{
		if($this->_model===null)
		{
			//if(!Yii::app()->user->isGuest)
				$this->_model=User::model()->findbyPk($id);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
