<?php

    Yii::import('core.widgets.TWidget');
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    class TMarkdownEditor extends TWidget {

        public $content;

        /**
         * 帮助model
         */
        public $model;
        public $scriptFile = array(
            'markdown/codemirror-2.34/lib/codemirror.js',
            'markdown/codemirror-2.34/lib/util/overlay.js',
            'markdown/codemirror-2.34/mode/xml/xml.js',
            'markdown/codemirror-2.34/mode/markdown/markdown.js',
            'markdown/codemirror-2.34/mode/gfm/gfm.js',
            'markdown/codemirror-2.34/mode/javascript/javascript.js',
            'markdown/marked/marked.js',
            'markdown/highlight.js/highlight.pack.js',
            'markdown/app.js',
        );
        public $cssFile = array(
            'markdown/vendor/normalize.css',
            'markdown/vendor/bootstrap.min.css',
            'markdown/codemirror-custom.css',
            'markdown/github-style.css',
            'markdown/vendor/styles/github.css',
            'markdown/style.css',
        );

        public function init() {
            parent::init();
            if (isset($model)) {
                $this->model = $model;
            }
        }

        public function run() {
            parent::run();
            $this->renderHeader();
            $this->renderSection();
        }

        public function renderHeader() {
            echo CHtml::openTag('header', array('id' => 'header'));
            echo CHtml::openTag('div', array('class' => 'navbar navbar-fixed-top'));
            echo CHtml::openTag('div', array('class' => 'navbar-inner'));
            echo CHtml::openTag('a', array('class' => 'brand', 'style' => 'font-size:14px;margin-top:5px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;'));
            echo $this->model->getAttributeLabel('title');
            echo CHtml::textField('title', $this->model->title, array('style' => 'margin-left:36px;margin-bottom:0px;'));
            echo CHtml::closeTag('a');
            echo CHtml::openTag('a', array('class' => 'brand', 'style' => 'font-size:14px;margin-top:5px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;'));
            echo $this->model->getAttributeLabel('main_id') . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            $this->widget('core.widgets.TTreeDropdown', array(
                'treeId' => 'main_id',
                'data' => HelpCategory::getHelpTree(),
                'options' => array(
                    'view' => array(
                        'showLine' => false,
                        'showIcon' => true,
                    )
                ),
                'defaultText' => $this->model->main->name ? $this->model->main->name : '未分类',
                'selectNode' => $this->model->main_id ? $this->model->main_id : ''
            ));
            echo CHtml::closeTag('a');

            //置顶
            echo CHtml::openTag('a', array('class' => 'brand', 'style' => 'font-size:14px;margin-top:5px;margin-right:0px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;'));
            echo CHtml::checkBox('top', $checked = $this->model->top ? true : false, array('style' => 'margin-left:36px;margin-bottom:0px;margin-right:0px'));
            echo CHtml::closeTag('a');
            echo CHtml::openTag('a', array('class' => 'brand', 'style' => 'font-size:14px;margin-top:8px;margin-left:0px;margin-bottom:0px;padding-top:0px;padding-bottom:0px;'));
            echo $this->model->getAttributeLabel('top');
            echo CHtml::closeTag('a');

            $this->controller->widget('bootstrap.widgets.TbButton', array(
                'label' => '关闭',
                'type' => 'primary',
                'htmlOptions' => array(
                    'class' => 'pull-right',
                    'style' => 'margin-left:6px;',
                    'id' => 'btnCancel'
                )
            ));
            $this->controller->widget('bootstrap.widgets.TbButton', array(
                'label' => '保存',
                'type' => 'danger',
                'htmlOptions' => array(
                    'style' => 'margin-left:6px;',
                    'class' => 'pull-right',
                    'id' => 'btnSave'
                )
            ));
            $this->controller->widget('bootstrap.widgets.TbButton', array(
                'label' => '预览',
                'htmlOptions' => array(
                    'class' => 'pull-right',
                    'id' => 'btnPrev'
                )
            ));
            echo CHtml::closeTag('div');
            echo CHtml::closeTag('div');
            echo CHtml::closeTag('header');
        }

        public function renderSection() {
            echo CHtml::openTag('section', array('id' => 'main'));
            echo CHtml::tag('textarea', array('id' => 'in'), $this->model->content);
            echo CHtml::tag('div', array('id' => 'out', 'class' => 'markdown-body'), '');
            echo CHtml::closeTag('section');
            echo CHtml::hiddenField('help_id', $this->model->id);
        }

    }

?>
