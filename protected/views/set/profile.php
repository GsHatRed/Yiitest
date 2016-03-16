<?php
//var_dump($dataProvider);
?>
<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'student_list',
    'dataProvider' => $dataProvider,
    'template' => "{pager}{items}",

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

    )
));
?>