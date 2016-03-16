<?php

Yii::import('zii.widgets.CPortlet');

class RecentComments extends CPortlet
{
	public $title='最近評論';
	public $maxComments=10;

	public function getRecentComments()
	{
		return Comment::model()->findRecentComments($this->maxComments);
	}

	protected function renderContent()
	{
		$this->render('recentComments');
	}
}