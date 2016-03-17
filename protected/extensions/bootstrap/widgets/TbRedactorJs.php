<?php

    /**
     * ## TbRedactorJs class file
     *
     * @author: antonio ramirez <antonio@clevertech.biz>
     * @copyright Copyright &copy; Clevertech 2012-
     * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
     */

    /**
     * ## TbRedactorJs class
     *
     * WYSIWYG editor based on Redactor library.
     * @see <http://imperavi.com/redactor/docs/>
     *
     * For updates of Redactor library see <https://github.com/yiiext/imperavi-redactor-widget> by Sam Dark.
     *
     * @package booster.widgets.forms.inputs.wysiwyg
     */
    class TbRedactorJS extends CInputWidget {

        /**
         * @var array {@link http://imperavi.com/redactor/docs/ redactor options}.
         */
        public $editorOptions = array();

        /**
         * @var string|null Selector pointing to textarea to initialize redactor for.
         * Defaults to null meaning that textarea does not exist yet and will be
         * rendered by this widget.
         */
        public $selector;

        /**
         * @var string Editor width
         */
        public $width = '100%';

        /**
         * @var string Editor height
         */
        public $height = '400px';

        public function init() {
            parent::init();

            if (!isset($this->editorOptions['lang'])) {
                $this->editorOptions['lang'] = Yii::app()->getLanguage();
            }

            if ($this->selector === null) {
                list($this->name, $this->id) = $this->resolveNameID();
                $this->htmlOptions['id'] = $this->id;
                $this->selector = '#' . $this->id;
                if (!array_key_exists('style', $this->htmlOptions)) {
                    $this->htmlOptions['style'] = "width:{$this->width};height:{$this->height};";
                }
                if ($this->hasModel()) {
                    echo CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
                } else {
                    echo CHtml::textArea($this->name, $this->value, $this->htmlOptions);
                }
            }
            $this->registerClientScript($this->id);
        }

        /**
         * Register required script files
         */
        public function registerClientScript() {
            Yii::app()->bootstrap->registerAssetCss('redactor.css');
            Yii::app()->bootstrap->registerAssetJs('redactor.min.js');
            // Prepend language file to scripts package.
            if ($this->editorOptions['lang'] != 'en') {
                Yii::app()->bootstrap->registerAssetJs('locales/redactor/' . $this->editorOptions['lang'] . '.js');
            }

            if (isset($this->editorOptions['plugins'])) {
                foreach ($this->editorOptions['plugins'] as $name) {
                    Yii::app()->bootstrap->registerAssetCss('redactor/plugins/' . $name . '.css');
                    Yii::app()->bootstrap->registerAssetJs('redactor/plugins/' . $name . '.js');
                }
            }

            $options = $this->editorOptions ? CJavaScript::encode($this->editorOptions) : '';

            Yii::app()->clientScript->registerScript(
                    uniqid(__CLASS__ . '#', true), "jQuery('{$this->selector}').redactor({$options});"
            );
        }

    }