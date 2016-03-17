<?php

/* ##  TbButtonColumn class file.
 *
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright  Copyright &copy; Christoffer Niska 2011-
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php) 
 * @package bootstrap.widgets
 * @since 0.9.8
 */

Yii::import('zii.widgets.grid.CButtonColumn');

/**
 * Bootstrap button column widget.
 * Used to set buttons to use Glyphicons instead of the defaults images.
 */
class TbButtonColumn extends CButtonColumn {

    /**
     * @var string the view button icon (defaults to 'eye-open').
     */
    public $viewButtonIcon = 'eye';

    /**
     * @var string the update button icon (defaults to 'pencil').
     */
    public $updateButtonIcon = 'pencil';

    /**
     * @var string the delete button icon (defaults to 'trash').
     */
    public $deleteButtonIcon = 'remove';

    /**
     * ### .initDefaultButtons()
     *
     * Initializes the default buttons (view, update and delete).
     */
    protected function initDefaultButtons() {
        if($this->deleteConfirmation===null)
            $this->deleteConfirmation=Yii::t('zii','Are you sure you want to delete this item?');
        
        if (!isset($this->buttons['delete']['click'])) {
            if (is_string($this->deleteConfirmation))
                $confirmation = CJavaScript::encode($this->deleteConfirmation);
            else
                $confirmation = '';

            if (Yii::app()->request->enableCsrfValidation) {
                $csrfTokenName = Yii::app()->request->csrfTokenName;
                $csrfToken = Yii::app()->request->csrfToken;
                $csrf = "\n\t\tdata:{ '$csrfTokenName':'$csrfToken' },";
            } else
                $csrf = '';

            if ($this->afterDelete === null)
                $this->afterDelete = 'function(){}';

            $this->buttons['delete']['click'] = <<<EOD
function(e) {
        var th = this,
                 afterDelete = $this->afterDelete;
        e.preventDefault();
        window.confirm({$confirmation}, function(ret){
            if(ret == false) return;  
            jQuery('#{$this->grid->id}').yiiGridView('update', {
                    type: 'POST',
                    url: jQuery(th).attr('href'),$csrf
                    success: function(data) {
                            jQuery('#{$this->grid->id}').yiiGridView('update');
                            afterDelete(th, true, data);
                    },
                    error: function(XHR) {
                            return afterDelete(th, false, XHR);
                    }
            });
        });
}
EOD;
        }
        parent::initDefaultButtons();

        if ($this->viewButtonIcon !== false && !isset($this->buttons['view']['icon']))
            $this->buttons['view']['icon'] = $this->viewButtonIcon;
        if ($this->updateButtonIcon !== false && !isset($this->buttons['update']['icon']))
            $this->buttons['update']['icon'] = $this->updateButtonIcon;
        if ($this->deleteButtonIcon !== false && !isset($this->buttons['delete']['icon']))
            $this->buttons['delete']['icon'] = $this->deleteButtonIcon;
    }

    /**
     * ### .renderButton()
     *
     * Renders a link button.
     *
     * @param string $id the ID of the button
     * @param array $button the button configuration which may contain 'label', 'url', 'imageUrl' and 'options' elements.
     * @param integer $row the row number (zero-based)
     * @param mixed $data the data object associated with the row
     */
    protected function renderButton($id, $button, $row, $data) {
        if (isset($button['visible']) && !$this->evaluateExpression($button['visible'], array('row' => $row, 'data' => $data)))
            return;
        $label = isset($button['label']) ? $this->evaluateExpression($button['label'], array('data' => $data, 'row' => $row)): $id;
        $url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data' => $data, 'row' => $row)) : '#';
        $options = isset($button['options']) ? $button['options'] : array();

        if (!isset($options['title']))
            $options['title'] = $label;

        if (!isset($options['rel']))
            $options['rel'] = 'tooltip';
        if (isset($button['icon'])) {
            if (strpos($button['icon'], 'icon') === false)
                $button['icon'] = 'icon-' . implode(' icon-', explode(' ', $button['icon']));

            echo CHtml::link('<i class="' . $button['icon'] . '"></i>'.$label, $url, $options);
        }
        else if (isset($button['imageUrl']) && is_string($button['imageUrl']))
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
        else
            echo CHtml::link($label, $url, $options);
    }

}
