<span>最近分享</span>
<?php 
$this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $article,
    'columns' =>  array(
        array(
            'name' => 'title',
            'value' => 'CHtml::link($data->title,"",array("class"=>"title","href"=> $data->url))',
            'type'=>'raw',
        ),
        array('name'=>'create_time','value'=>'date("Y-m-d",$data->create_time)'),
        array('name'=>'comment','value'=>'$data->commentCount')
    )
));
?>