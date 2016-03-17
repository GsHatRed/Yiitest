/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
        CKEDITOR.dialog.add('suseranddept', function(editor) {
            var acceptedClass = {
                user: 1,
                dept: 1
            };
            return {
                title: editor.lang.tdforms.suseranddept.dialogTitle,
                minWidth: 350,
                minHeight: 150,
                onShow: function() {
                    delete this.textField;

                    var element = this.getParentEditor().getSelection().getSelectedElement();
                    if (element && element.getName() == "img" && (acceptedClass[ element.getAttribute('class') ] || !element.getAttribute('type'))) {
                        this.textField = element;
                        this.setupContent(element);
                    }
                },
                onOk: function() {
                    var editor = this.getParentEditor(),
                            element = this.textField,
                            fieldId = '',
                            widget_json = '',
                            field_name = this.getValueOf('info', 'title'),
                            field_type = this.getValueOf('info', 'type'),
                            height = this.getValueOf('info', 'wheight'),
                            width = this.getValueOf('info', 'wwidth'),
                            modify = this.getValueOf('info', 'modify'),
                            isInsertMode = !element;
                    widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
                            + '"field_attr":' + '{' + '"modify":' + '"' + modify + '",' + '"height":' + '"' + height + '",' + '"width":' + '"' + width + '",' + '"title":' + '"' + field_name + '",' + '"class":' + '"' + field_type + '",'
                            + '"value":""' + '}}';
                    if (height)
                        $(element.$).attr({
                            "w_height": height
                        });
                    if (width)
                        $(element.$).attr({
                            "w_width": width
                        });
                    if (isInsertMode) {
                        element = editor.document.createElement('img');
                        element.setAttribute('src', baseUrl + '/images/form/suseranddept.png');
                        element.setAttribute('w_height', height);
                        element.setAttribute('w_width', width);
                        fieldId = $.ajax({
                            type: "post",
                            url: g_requestURL,
                            data: {
                                fieldInfo: widget_json,
                                formID: g_form_id
                            },
                            cache: false,
                            async: false
                        }).responseText;
                        if (fieldId == 'repeat') {
                            _originalAlert(field_name + ' 字段已存在！');
                            return false;
                        } else {
                            element.setAttribute('name', "data_" + fieldId);
                            element.setAttribute('id', 'data_' + fieldId);
                        }
                    }




                    // Element might be replaced by commitment.
                    if (!isInsertMode) {
                        fieldId = element.getAttribute('name').split('_')[1];
                        fieldId = $.ajax({
                            type: "post",
                            url: g_requestURL,
                            data: {
                                fieldInfo: widget_json,
                                formID: g_form_id,
                                fieldId: fieldId
                            },
                            cache: false,
                            async: false
                        }).responseText;
                        if (fieldId == 'repeat') {
                            _originalAlert(field_name + ' 字段已存在！');
                            return false;
                        }
                    }
                    element.setAttribute('class', field_type);
                    element.setAttribute('title', field_name);
                    element.setAttribute('modify', modify);
                    this.commitContent(element);
                    if (isInsertMode) {
                        editor.insertElement(element);
                    }
                },
                contents: [
                    {
                        id: 'info',
                        elements: [
                            {
                                id: 'title',
                                type: 'text',
                                label: editor.lang.common.controlName,
                                labelLayout: 'horizontal',
                                validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.common.validateControlFailed),
                                'default': '',
                                accessKey: 'N',
                                onShow: function() {
                                    var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                                    if (element)
                                        this.setValue(element.getAttribute('title'));
                                },
                                commit: function(element) {
                                    if (this.getValue())
                                        element.setAttribute('title', this.getValue());
                                    else {
                                        element.removeAttribute('title');
                                    }
                                }
                            },
                            {
                                id: 'type',
                                type: 'select',
                                label: editor.lang.common.value,
                                labelLayout: 'horizontal',
                                'default': 'user',
                                accessKey: 'M',
                                items: [
                                    [editor.lang.tdforms.suseranddept.user, 'user'],
                                    [editor.lang.tdforms.suseranddept.dept, 'dept']
                                ],
                                onShow: function() {
                                    var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                                    if (element)
                                        this.setValue(element.getAttribute('class'));
                                }
                            },
                            {
                                type: 'text',
                                id: 'wheight',
                                labelLayout: 'horizontal',
                                label: editor.lang.widget.wheight,
                                validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                                setup: function(element) {
                                    this.setValue(element.getAttribute('w_height') ? element.getAttribute('w_height') : '');
                                }
                            },
                            {
                                type: 'text',
                                labelLayout: 'horizontal',
                                id: 'wwidth',
                                label: editor.lang.widget.wwidth,
                                validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                                setup: function(element) {
                                    this.setValue(element.getAttribute('w_width') ? element.getAttribute('w_width') : '');
                                }
                            },
                            {
                                type: 'hbox',
                                children: [
                                    {
                                        type: 'html',
                                        html: '<span>' + CKEDITOR.tools.htmlEncode(editor.lang.tdforms.suseranddept.enableEdit) + '</span>'
                                    },
                                    {
                                        id: 'modify',
                                        type: 'checkbox',
                                        label: '',
                                        labelLayout: 'horizontal',
                                        'default': '',
                                        accessKey: 'M',
                                        value: "checked",
                                        setup: function(element) {
                                            this.setValue(element.getAttribute('modify') == "true" ? 'checked' : '');
                                        }
                                    }
                                ]
                            },
                        ]
                    }
                ]
            };
        });
