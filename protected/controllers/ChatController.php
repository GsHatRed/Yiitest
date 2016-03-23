<?php 
class ChatController extends Controller{
	
	public function actionIndex(){
		$model = new Chat;
		$model->unSetAttributes();
		$dataProvider = new CActiveDataProvider('Chat', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['postsPerPage'],
			),
			//'criteria'=>$criteria,
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
			if($model->save())
				$this->redirect(array('index'));
		}
		$this->render('index',array(
			'model' => $model,
			'dataProvider' => $dataProvider
			));
	}

	public function actionChats(){

	}

	public function loadModelByUser(){
		$model = Chat::model()->findByPk(Yii::app()->user->id);
		return $model;
	}
}