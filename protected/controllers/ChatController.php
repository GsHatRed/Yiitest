<?php 
class ChatController extends Controller{
	
	public function actionIndex(){
		$model = new Chat;
		$model->unSetAttributes();
		$dataProvider = new CActiveDataProvider('Chat', array(
			'pagination'=>array(
				'pageSize'=>Yii::app()->params['postsPerPage'],
			),
			'criteria'=>array(
				'order' =>  'date desc',
				'limit' => '20'
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

	}

	public function actionPrise(){
		$id = $_POST['id'];
		$model = $this->loadModelById($id);
		$model->priseCount();
		//if($model->save()){
			echo 'ok';
		// }else{
		// 	echo 'no';
		// }
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