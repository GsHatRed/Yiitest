/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
        CKEDITOR.dialog.add('flowcomment', function(editor) {
            return {
                title: editor.lang.tdforms.flowcomment.dialogTitle,
                minWidth: 350,
                minHeight: 220,
                onShow: function() {
                    delete this.flowcomment;

                    var element = this.getParentEditor().getSelection().getSelectedElement();
                    if (element && element.getName() == "img") {
                        this.flowcomment = element;
                        this.setupContent(element);
                    }
                },
                onOk: function() {
                    var editor,
                            element = this.flowcomment,
                            fieldId = '',
                            widget_json = '',
                            field_name = this.getValueOf('info', 'title'),
                            field_type = 'flowcomment',
                            isInsertMode = !element,
                            height = this.getValueOf('info', 'height'),
                            width = this.getValueOf('info', 'width'),
                            fontSize = this.getValueOf('info', 'fontSize');
                    widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
                            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"class":' + '"' + field_type + '",'
                            + '"height":' + '"' + height + '",' + '"width":' + '"' + width + '",' + '"fontSize":' + '"' + fontSize + '"' + '}}';

                    if (isInsertMode) {
                        editor = this.getParentEditor();
                        element = editor.document.createElement('img');
                        element.setAttribute('src', baseUrl + '/images/form/flowcomment.png');
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
                            element.setAttribute('name', 'data_' + fieldId);
                            element.setAttribute('id', 'data_' + fieldId);
                        }
                    }
                    $(element.$).removeAttr('style');
                    if (fontSize)
                        $(element.$).css({
                            "font-size": fontSize + "px"
                        });
                    if (height)
                        $(element.$).css({
                            "height": height + "px"
                        });
                    if (width)
                        $(element.$).css({
                            "width": width + "px"
                        });
                    this.commitContent(element);

                    if (isInsertMode) {
                        editor.insertElement(element);
                    }

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
                    element.setAttribute( 'class', field_type );
                    element.setAttribute( 'title', field_name);
                },
                contents: [
                    {
                        id: 'info',
                        elements: [
                            {
                                type: 'hbox',
                                widths: ['50%', '50%'],
                                children: [
                                    {
                                        id: 'title',
                                        type: 'text',
                                        label: editor.lang.common.controlName,
                                        validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.common.validateControlFailed),
                                        'default': '',
                                        accessKey: 'N',
                                        setup: function(element) {
                                            this.setValue(element.getAttribute('title') || '');
                                        },
                                        commit: function(element) {
                                            if (this.getValue())
                                                element.setAttribute('title', this.getValue());
                                        }
                                    },
                                    {
                                        id: 'fontSize',
                                        type: 'text',
                                        label: editor.lang.tdforms.tdtextfield.fontSize,
                                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                                        setup: function(element) {
                                            this.setValue(element.$.style.fontSize.substr(0, element.$.style.fontSize.length - 2));
                                        }
                                    }
                                ]
                            },
                            {
                                type: 'hbox',
                                widths: ['50%', '50%'],
                                children: [
                                    {
                                        id: 'height',
                                        type: 'text',
                                        label: editor.lang.tdforms.flowcomment.height,
                                        'default': '',
                                        accessKey: 'C',
                                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                                        setup: function(element) {
                                            this.setValue(element.$.style.height.substr(0, element.$.style.height.length - 2));
                                        }
                                    },
                                    {
                                        id: 'width',
                                        type: 'text',
                                        label: editor.lang.tdforms.flowcomment.width,
                                        'default': '',
                                        accessKey: 'R',
                                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                                        setup: function(element) {
                                            this.setValue(element.$.style.width.substr(0, element.$.style.width.length - 2));
                                        }
                                    }
                                ]
                            }

                        ]
                    }
                ]
            };
        });
