<?php

Yii::import('zii.widgets.CPortlet');

class TagCloud extends CPortlet
{
	public $title='標籤';
	public $maxTags=20;

	protected function renderContent()
	{
		$tags=Tag::model()->findTagWeights($this->maxTags);
		Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . "/static/css/ie.css"); 
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/static/js/3d.js",2); 
		echo "<div id='div1'>";
		foreach($tags as $tag=>$weight)
		{
			$color = rand(0,3);
			$link=CHtml::link(CHtml::encode($tag), array('post/index','tag'=>$tag), array('class'=>Yii::app()->params['color'][$color]));
			echo $link;//CHtml::tag('span', array(
			// 	'class'=>'tag',
			// 	'style'=>"font-size:{$weight}pt",
			// ), $link)."\n";
		}
		echo '</div>';
	}
}