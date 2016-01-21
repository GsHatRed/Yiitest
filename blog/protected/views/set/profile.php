<?php
//var_dump($dataProvider);
?>
<?php

$this->widget('bootstrap.widgets.TbGroupGridView', array(
    'id' => 'student_list',
    'dataProvider' => $dataProvider,
    'template' => "{toolbar}{pager}{items}",
    'extraRowCssClass' => 'td-group',
    'pagerCssClass' => 'td-pager',
    'columns' => array(
        array(
            'class' => 'CCheckBoxColumn',
            'selectableRows' => 2,
            'value' => '$data->id',
            'headerHtmlOptions' => array('width' => '10px'),
            'checkBoxHtmlOptions' => array('name' => 'selectdel[]', 'style' => 'margin-top:-3px;'),
            'htmlOptions' => array('class' => 'notopen'),
        ),
        array(
            'name' => 'login_name',
        ),
        array(
            'name' => 'student_id',
        ),
        array(
            'name' => 'name',
            'value' => 'CHtml::link($data->name, array("view","id" => $data->id,"type" => "student"),array("title"=>$data->name))',
            'type' => 'raw',
            'htmlOptions' => array('class' => 'name'),
        ),
        array(
            'name' => 'mobile_phone',
        ),
        array(
            'header' => '最后访问时间',
            'name' => 'last_visit_time',
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{update}{delete}',
            'header' => '操作',
            'buttons' => array(
                'update' => array(
                    'label' => '修改',
                    'url' => 'Yii::app()->controller->createUrl("update",array("id" => $data->id,"type" => "student"))',
                ),
                'delete' => array(
                    'label' => '删除',
                    'options' => array(
                        'class' => 'td-link-icon',
                    ),
                ),
            ),
            'htmlOptions' => array('style' => 'width: 160px;text-align:center;'),
            'headerHtmlOptions' => array('style' => 'width: 160px;text-align:center;'),
        ),
    )
));
?>