<?php

/**
 * TbInputHorizontal class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets.input
 */
Yii::import('bootstrap.widgets.input.TbInput');

/**
 * Bootstrap horizontal form input widget.
 * @since 0.9.8
 */
class TbInputHorizontal extends TbInput
{

    /**
     * Runs the widget.
     */
    public function run()
    {
        $rowHtmlOptions = array();
        if ($this->htmlOptions['rowHtmlOptions']) {
            $rowHtmlOptions = $this->htmlOptions['rowHtmlOptions'];
            unset($this->htmlOptions['rowHtmlOptions']);
        }
        $rowHtmlOptions = array_merge(array('class' => 'control-group ' . $this->getContainerCssClass()), $rowHtmlOptions);
        echo CHtml::openTag('div', $rowHtmlOptions);
        parent::run();
        echo '</div>';
    }

    /**
     * Returns the label for this block.
     * @return string the label
     */
    protected function getLabel()
    {
        if (isset($this->labelOptions['class']))
            $this->labelOptions['class'] .= ' control-label';
        else
            $this->labelOptions['class'] = 'control-label';

        if (isset($this->htmlOptions['id']))
            $this->labelOptions['for'] = $this->htmlOptions['id'];

        return parent::getLabel();
    }

    /**
     * Renders a checkbox.
     * @return string the rendered content
     */
    protected function checkBox()
    {
        $attribute = $this->attribute;
        echo '<div class="controls">';
        echo '<label class="checkbox" for="' . $this->getAttributeId($attribute) . '">';
        echo $this->form->checkBox($this->model, $attribute, $this->htmlOptions) . PHP_EOL;
        echo $this->model->getAttributeLabel($attribute);
        echo $this->getError() . $this->getHint();
        echo '</label></div>';
    }

    /**
     * Renders a toogle button
     * @return string the rendered content
     */
    protected function toggleButton()
    {
        // widget configuration is set on htmlOptions['options']
        $options = array(
            'model' => $this->model,
            'attribute' => $this->attribute
        );
        if (isset($this->htmlOptions['options'])) {
            $options = CMap::mergeArray($options, $this->htmlOptions['options']);
            unset($this->htmlOptions['options']);
        }
        $options['htmlOptions'] = $this->htmlOptions;

        echo $this->getLabel();
        echo '<div class="controls">';
        $this->widget('bootstrap.widgets.TbToggleButton', $options);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a list of checkboxes.
     * @return string the rendered content
     */
    protected function checkBoxList()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->checkBoxList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a list of inline checkboxes.
     * @return string the rendered content
     */
    protected function checkBoxListInline()
    {
        $this->htmlOptions['inline'] = true;
        $this->checkBoxList();
    }

    /**
     * Renders a list of checkboxes using Button Groups.
     * @return string the rendered content
     */
    protected function checkBoxGroupsList()
    {
        if (isset($this->htmlOptions['for']) && !empty($this->htmlOptions['for'])) {
            $label_for = $this->htmlOptions['for'];
            unset($this->htmlOptions['for']);
        } else if (isset($this->data) && !empty($this->data)) {
            $label_for = CHtml::getIdByName(get_class($this->model) . '[' . $this->attribute . '][' . key($this->data) . ']');
        }

        if (isset($label_for)) {
            $this->labelOptions = array('for' => $label_for);
        }

        $this->htmlOptions['class'] = 'pull-left';

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->checkBoxGroupsList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a drop down list (select).
     * @return string the rendered content
     */
    protected function dropDownList()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->dropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a drop down tree (select).
     * @return string the rendered content
     */
    protected function dropDownTree()
    {
        $options = array(
            'formModel' => $this->model,
            'formAttribute' => $this->attribute,
            'form' => $this->form,
            'data' => $this->data,
        );
        $options = array_merge($options, $this->htmlOptions);


        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('core.widgets.TTreeDropdown', $options);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a file field.
     * @return string the rendered content
     */
    protected function fileField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->fileField($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a extended file field which can select several files.
     * @return string the rendered content
     */
    protected function fileFieldExt()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        //$id = ucfirst(get_class($this->model)).'['.$this->attribute.']';
        $this->widget('bootstrap.widgets.TbFileSelector', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => $options,
            'htmlOptions' => $this->htmlOptions,
        ));
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a password field.
     * @return string the rendered content
     */
    protected function passwordField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->passwordField($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a radio button.
     * @return string the rendered content
     */
    protected function radioButton()
    {
        $attribute = $this->attribute;
        echo '<div class="controls">';
        echo '<label class="radio" for="' . $this->getAttributeId($attribute) . '">';
        echo $this->form->radioButton($this->model, $attribute, $this->htmlOptions) . PHP_EOL;
        echo $this->model->getAttributeLabel($attribute);
        echo $this->getError() . $this->getHint();
        echo '</label></div>';
    }

    /**
     * Renders a list of radio buttons.
     * @return string the rendered content
     */
    protected function radioButtonList()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->radioButtonList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a list of inline radio buttons.
     * @return string the rendered content
     */
    protected function radioButtonListInline()
    {
        $this->htmlOptions['inline'] = true;
        $this->radioButtonList();
    }

    /**
     * Renders a list of radio buttons using Button Groups.
     * @return string the rendered content
     */
    protected function radioButtonGroupsList()
    {
        if (isset($this->htmlOptions['for']) && !empty($this->htmlOptions['for'])) {
            $label_for = $this->htmlOptions['for'];
            unset($this->htmlOptions['for']);
        } else if (isset($this->data) && !empty($this->data)) {
            $label_for = CHtml::getIdByName(get_class($this->model) . '[' . $this->attribute . '][' . key($this->data) . ']');
        }

        if (isset($label_for)) {
            $this->labelOptions = array('for' => $label_for);
        }

        $this->htmlOptions['class'] = 'pull-left';

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->radioButtonGroupsList($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a textarea.
     * @return string the rendered content
     */
    protected function textArea()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->form->textArea($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a text field.
     * @return string the rendered content
     */
    protected function textField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);

        //检查是否支持多语言输入字段
        $attributes = $this->model->behaviors()['TranslationBehavior']['attributes'];
        if (!empty($attributes) && !$this->model->isNewRecord) {
            $isMultiLang = SysParams::getParams('multi_language');
            if ($isMultiLang && in_array($this->attribute, $attributes)) {
                $this->widget('core.widgets.TTranslate', array(
                    'model' => $this->model,
                    'attribute' => $this->attribute,
                ));
            }
        }

        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a masked text field.
     * @return string the rendered content
     */
    protected function maskedTextField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->maskedTextField($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a CAPTCHA.
     * @return string the rendered content
     */
    protected function captcha()
    {
        echo $this->getLabel();
        echo '<div class="controls"><div class="captcha">';
        echo '<div class="widget">' . $this->widget('CCaptcha', $this->captchaOptions, true) . '</div>';
        echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getError() . $this->getHint();
        echo '</div></div>';
    }

    /**
     * Renders an uneditable field.
     * @return string the rendered content
     */
    protected function uneditableField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo CHtml::tag('span', $this->htmlOptions, $this->model->{$this->attribute});
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a datepicker field.
     * @return string the rendered content
     * @author antonio ramirez <antonio@clevertech.biz>
     */
    protected function datepickerField()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        if (isset($this->htmlOptions['events'])) {
            $events = $this->htmlOptions['events'];
            unset($this->htmlOptions['events']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbDatePicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'events' => isset($events) ? $events : array(),
            'htmlOptions' => $this->htmlOptions,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a datetimepicker field.
     * @return string the rendered content
     * @author antonio ramirez <antonio@clevertech.biz>
     */
    protected function datetimepickerField()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbDateTimePicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'htmlOptions' => $this->htmlOptions,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a colorpicker field.
     * @return string the rendered content
     * @author antonio ramirez <antonio@clevertech.biz>
     */
    protected function colorpickerField()
    {
        $format = 'hex';
        if (isset($this->htmlOptions['format'])) {
            $format = $this->htmlOptions['format'];
            unset($this->htmlOptions['format']);
        }
        if (isset($this->htmlOptions['events'])) {
            $events = $this->htmlOptions['events'];
            unset($this->htmlOptions['events']);
        }
        if (isset($this->htmlOptions['displayInput'])) {
            $displayInput = $this->htmlOptions['displayInput'];
            unset($this->htmlOptions['displayInput']);
        }

        if (isset($this->htmlOptions['value'])) {
            $value = $this->htmlOptions['value'];
        }
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbColorPicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'format' => $format,
            'value' => isset($value) ? $value : "",
            'displayInput' => isset($displayInput) ? $displayInput : true,
            'events' => isset($events) ? $events : array(),
            'htmlOptions' => $this->htmlOptions,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a redactor.
     * @return string the rendered content
     */
    protected function redactorJs()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }
        if (isset($this->htmlOptions['width'])) {
            $width = $this->htmlOptions['width'];
            unset($this->htmlOptions['width']);
        }
        if (isset($this->htmlOptions['height'])) {
            $height = $this->htmlOptions['height'];
            unset($this->htmlOptions['height']);
        }
        echo $this->getLabel();
        echo '<div class="controls">';
        $this->widget('bootstrap.widgets.TbRedactorJs', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'editorOptions' => isset($options) ? $options : array(),
            'width' => isset($width) ? $width : '100%',
            'height' => isset($height) ? $height : '400px',
            'htmlOptions' => $this->htmlOptions
        ));
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a Markdown Editor.
     * @return string the rendered content
     */
    protected function markdownEditorJs()
    {

        if (isset($this->htmlOptions['width'])) {
            $width = $this->htmlOptions['width'];
            unset($this->htmlOptions['width']);
        }
        if (isset($this->htmlOptions['height'])) {
            $height = $this->htmlOptions['height'];
            unset($this->htmlOptions['height']);
        }
        echo $this->getLabel();
        echo '<div class="controls">';
        echo '<div class="wmd-panel">';
        echo '<div id="wmd-button-bar" class="btn-toolbar"></div>';
        $this->widget('bootstrap.widgets.TbMarkdownEditorJs', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'width' => isset($width) ? $width : '100%',
            'height' => isset($height) ? $height : '400px',
            'htmlOptions' => $this->htmlOptions
        ));
        echo $this->getError() . $this->getHint();
        echo '<div id="wmd-preview" class="wmd-panel wmd-preview" style="width:' . (isset($width) ? $width : '100%') . '"></div>';
        echo '</div>'; // wmd-panel
        echo '</div>'; // controls
    }

    /**
     * Renders Bootstrap wysihtml5 editor.
     * @return mixed|void
     */
    protected function html5Editor()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }
        if (isset($this->htmlOptions['width'])) {
            $width = $this->htmlOptions['width'];
            unset($this->htmlOptions['width']);
        }
        if (isset($this->htmlOptions['height'])) {
            $height = $this->htmlOptions['height'];
            unset($this->htmlOptions['height']);
        }
        echo $this->getLabel();
        echo '<div class="controls">';
        $this->widget('bootstrap.widgets.TbHtml5Editor', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'editorOptions' => isset($options) ? $options : array(),
            'width' => isset($width) ? $width : '100%',
            'height' => isset($height) ? $height : '400px',
            'htmlOptions' => $this->htmlOptions
        ));
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a ckEditor.
     * @return string the rendered content
     * @author antonio ramirez <antonio@clevertech.biz>
     */
    protected function ckEditor()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        $this->widget('bootstrap.widgets.TbCKEditor', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'editorOptions' => isset($options) ? $options : array(),
            'htmlOptions' => $this->htmlOptions
        ));
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a daterange field.
     * @return string the rendered content
     * @author antonio ramirez <antonio@clevertech.biz>
     */
    protected function dateRangeField()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        if (isset($options['callback'])) {
            $callBack = $options['callback'];
            unset($options['callback']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbDateRangePicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'callback' => isset($callback) ? $callback : array(),
            'htmlOptions' => $this->htmlOptions,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a timepicker field.
     * @return string the rendered content
     * @author Sergii Gamaiunov <hello@webkadabra.com>
     */
    protected function timepickerField()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        if (isset($this->htmlOptions['events'])) {
            $events = $this->htmlOptions['events'];
            unset($this->htmlOptions['events']);
        }

        echo $this->getLabel();
        echo '<div class="controls bootstrap-timepicker">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbTimePicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'events' => isset($events) ? $events : array(),
            'htmlOptions' => $this->htmlOptions,
            'form' => $this->form
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a timepicker field.
     * @return string the rendered content
     * @author Sergii Gamaiunov <hello@webkadabra.com>
     */
    protected function timepickerExtField()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        if (isset($this->htmlOptions['events'])) {
            $events = $this->htmlOptions['events'];
            unset($this->htmlOptions['events']);
        }

        if (isset($this->htmlOptions['ext'])) {
            $ext = $this->htmlOptions['ext'];
            unset($this->htmlOptions['ext']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();

        if (isset($ext['beforeLabel']))
            echo CHtml::label($ext['beforeLabel'], false, array('class' => 'radio inline'));
        echo $this->form->textField($this->model, $ext['attribute'], $ext['options']);
        if (isset($ext['afterLabel']))
            echo CHtml::label($ext['afterLabel'], false, array('class' => 'radio inline'));
        echo '<span class="bootstrap-timepicker">';
        $this->widget('bootstrap.widgets.TbTimePicker', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'events' => isset($events) ? $events : array(),
            'htmlOptions' => $this->htmlOptions,
            'form' => $this->form
        ));
        echo '</span>';
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a select2Field
     * @return mixed|void
     */
    protected function select2Field()
    {
        if (isset($this->htmlOptions['options'])) {
            $options = $this->htmlOptions['options'];
            unset($this->htmlOptions['options']);
        }

        if (isset($this->htmlOptions['events'])) {
            $events = $this->htmlOptions['events'];
            unset($this->htmlOptions['events']);
        }

        if (isset($this->htmlOptions['data'])) {
            $data = $this->htmlOptions['data'];
            unset($this->htmlOptions['data']);
        }

        if (isset($this->htmlOptions['asDropDownList'])) {
            $asDropDownList = $this->htmlOptions['asDropDownList'];
            unset($this->htmlOptions['asDropDownList']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('bootstrap.widgets.TbSelect2', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'options' => isset($options) ? $options : array(),
            'events' => isset($events) ? $events : array(),
            'data' => isset($data) ? $data : array(),
            'asDropDownList' => isset($asDropDownList) ? $asDropDownList : true,
            'htmlOptions' => $this->htmlOptions,
            'form' => $this->form
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a typeAhead field.
     * @return string the rendered content
     */
    protected function typeAheadField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->typeAheadField($this->model, $this->attribute, $this->data, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a number field.
     * @return string the rendered content
     */
    protected function numberField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->numberField($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * 渲染一个文本域选择控件
     * @return void
     */
    protected function selector()
    {
        
        if (isset($this->htmlOptions['type'])) {
            $type = $this->htmlOptions['type'];
            unset($this->htmlOptions['type']);
        }

        if (isset($this->htmlOptions['single'])) {
            $single = $this->htmlOptions['single'];
            unset($this->htmlOptions['single']);
        }

        if (isset($this->htmlOptions['codeValue'])) {
            $codeValue = $this->htmlOptions['codeValue'];
            unset($this->htmlOptions['codeValue']);
        }

        if (isset($this->htmlOptions['addButtonOptions'])) {
            $addButtonOptions = $this->htmlOptions['addButtonOptions'];
            unset($this->htmlOptions['addButtonOptions']);
        }

        if (isset($this->htmlOptions['delButtonOptions'])) {
            $delButtonOptions = $this->htmlOptions['delButtonOptions'];
            unset($this->htmlOptions['delButtonOptions']);
        }

        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();

        $this->widget('core.widgets.TSelector', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'type' => isset($type) ? $type : '',
            'single' => isset($single) ? true : false,
            'codeValue' => isset($codeValue) ? true : false,
            'form' => $this->form,
            'htmlOptions' => $this->htmlOptions,
            'textFieldHtmlOptions' => $this->htmlOptions,
            'addButtonOptions' => isset($addButtonOptions) ? $addButtonOptions : false,
            'delButtonOptions' => isset($delButtonOptions) ? $delButtonOptions : false,
        ));

        echo $this->getError() . $this->getHint();
        echo $this->getAppend();
        echo '</div>';
    }

    protected function remind()
    {
        $options = array(
            'type' => $this->htmlOptions['type'],
        );

        echo '<label class="control-label" for="TRemind_type">提醒</label>';
        echo '<div class="controls">';
        $this->widget('core.widgets.TRemind', $options);
        echo '</div>';
    }


    /**
     * Renders a colorselector field.
     * @return string the rendered content
     * @author fl <fl@tongda2000.com>
     */
    protected function colorselectorField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('core.widgets.TColorSelector', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a iconselector field.
     * @return string the rendered content
     * @author fl <fl@tongda2000.com>
     */
    protected function iconSelectorField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('core.widgets.TIconSelector', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    /**
     * Renders a tagselector field.
     * @return string the rendered content.
     * @author fl <fl@tongda2000.com>
     */
    protected function tagselectorField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        $this->widget('core.widgets.TTagSelector', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
        ));
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }

    protected function translate()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->form->textField($this->model, $this->attribute, $this->htmlOptions);
        echo $this->getAppend();
        echo $this->getError();
        echo '</div>';

    }

    protected function spineditField()
    {
        echo $this->getLabel();
        echo '<div class="controls">';
        echo $this->getPrepend();
        echo $this->getAppend();
        echo $this->getError() . $this->getHint();
        echo '</div>';
    }
}
