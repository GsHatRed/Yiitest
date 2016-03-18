/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.dialog.add('remove', function(editor) {

    return {
        title: editor.lang.common.removeTitle,
        minWidth: 190,
        minHeight: 30,
        contents: [
            {
                id: 'tab1',
                label: '',
                title: '',
                expand: true,
                padding: 0,
                elements: [
                    {
                        type: 'html',
                        html: '<span>' + CKEDITOR.tools.htmlEncode(editor.lang.common.removeTips) + '</span>'
                    }
                ]
            }
        ],
        onShow: function() {
            var selection = editor.getSelection();
            this.element = selection.getStartElement();
        },
        onOk: function() {
            var element = this.element;
            var data_id = '',
                    $remove;

            if (element.getAttribute('type') === 'radio') {
                data_id = $(element.$).parent().attr('id');
                $remove = $(element.$).parent();
            } else {
                data_id = element.getAttribute('id');
                $remove = $(element.$);
            }
            if (data_id) {
                data_id = data_id.split('_')[1];
            }
            var selection = editor.getSelection();
            var target = selection.getSelectedElement();
            var ranges = selection.getRanges();
            var range = ranges ? ranges[0] : null;
            target && range && range.moveToElementEditablePosition(target);
            range.select(true);
            $remove.remove();

            $.ajax({type: "post", url: g_removeURL, data: {id: data_id, formID: g_form_id}, cache: false, async: false});
            $('#field_data_' + data_id).remove();
        },
        buttons: [CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton]
    };
});
