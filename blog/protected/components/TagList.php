<?php

Yii::import('zii.widgets.CPortlet');

class TagList extends CPortlet
{
	public $title='標籤';
	public $maxTags=20;

	protected function renderContent()
	{
		$tags=Tag::model()->findTagWeights($this->maxTags);

		foreach($tags as $tag=>$weight)
		{
			$class = 'tag taglist';
			if(isset($_GET['tag']))
				if($tag==$_GET['tag']){
					$class .= ' active';
				}
			$link=CHtml::link(CHtml::encode($tag), array('post/tags','tag'=>$tag));
			echo CHtml::tag('span', array(
				'class'=>$class,
				'style'=>"font-size:{$weight}pt",
			), $link)."\n";
		}
	}
}