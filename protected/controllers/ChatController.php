<?php 
class ChatController extends Controller{
	public $layout='column2';
	public function actionIndex(){
		$model = new Chat;
		$model->unSetAttributes();
		$dataProvider = new CActiveDataProvider('Chat', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['postsPerPage'],
			),
			'criteria'=>array(
				'order' =>  'date desc',
				),
		));
		if(isset($_POST['ajax']) && $_POST['ajax']==='chat-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		if(isset($_POST['Chat']))
		{
			$model->user_id = Yii::app()->user->id;
			$model->content = $_POST['Chat']['content'];
			$model->date = time();
			if($model->save())
				$this->redirect(array('index'));
			// else
			// 	var_dump($model->errors);
		}
		$this->render('index',array(
			'model' => $model,
			'dataProvider' => $dataProvider
			));
	}

	public function actionChats(){
		if(Yii::app()->user->isGuest){
			echo '请先登录';
			Yii::app()->end();
		}
	}

	public function actionDelete(){
		if(Yii::app()->user->isGuest){
			echo '请先登录';
			Yii::app()->end();
		}
		$this->loadModelById($_POST['id'])->delete();
		echo 'ok';
	}

	public function actionPraise(){
		if(Yii::app()->user->isGuest){
			echo '请先登录';
			Yii::app()->end();
		}

		$id = $_POST['id'];
		$isCheckPraise = Praise::checkIsPraise($id,'chat');
		if(!$isCheckPraise){
			$model = $this->loadModelById($id);
			$model->praiseCount();
			echo 'ok';
		}else{
			echo '你不爱我了吗?!';
		}
	}

	public function loadModelByUser(){
		$model = Chat::model()->findByPk(Yii::app()->user->id);
		return $model;
	}
	public function loadModelById($id){
		$model = Chat::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		else
			return $model;
	}
}