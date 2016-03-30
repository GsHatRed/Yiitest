<?php
/**
 * 带遮罩的进度条
 *
 * @author fl <fl@tongda2000.com>
 */

Yii::import('core.widgets.TWidget');

class TProgressBar extends TWidget{
    /**
     * 0-100周期
     */
    public $duration = 3000;

    /**
     *
     * @var string 完成事件
     */
    public $onComplete=null;

    public function init() {
        parent::init();
        if($this->onComplete !== '') {
            $this->onComplete = CJavaScript::encode($this->onComplete);
        }
        $this->registerJs();
    }

    public function run() {
        $this->beginWidget('bootstrap.widgets.TbModal', array('id' => $this->id, 'options' => array("backdrop" => 'static')));
        echo '<div class="modal-header">';
        echo '<h3>升级程序执行中...</h3>';
        echo '</div>';
        echo '<div class="modal-body">';
        echo '<p>';
        $this->widget('bootstrap.widgets.TbProgress', array(
            'percent'=>0,
            'striped'=>true,
            'animated'=>true,
            'htmlOptions' => array('id'=> $this->id .'-pb')
        ));
        echo '</p>';
        echo '</div>';
        $this->endWidget();
    }

    protected function registerJs() {
        $js = <<<EOD
            \$("#{$this->id}").on('shown', function(){
                if({$this->onComplete} === null)
                    \$(".bar",this).animate({"width":"100%"}, 3000);
                else
                    \$(".bar",this).animate({"width":"100%"}, 3000,{$this->onComplete});
            });
EOD;
        Yii::app()->clientScript->registerScript(__CLASS__.'#'.$this->id, $js);
    }
}

?>
