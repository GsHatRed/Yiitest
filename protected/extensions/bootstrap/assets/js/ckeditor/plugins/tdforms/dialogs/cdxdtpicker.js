/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
//日历控件
CKEDITOR.dialog.add('cdxdtpicker', function (editor) {
    var acceptedTypes = {
        date: 'yyyy-MM-dd',
        datetime: 'yyyy-MM-dd HH:mm:ss',
        custom: 1
    };
    return {
        title: editor.lang.tdforms.cdxdtpicker.dialogTitle,
        minWidth: 350,
        minHeight: 150,
        onShow: function () {
            delete this.cdxdtpicker;

            var element = this.getParentEditor().getSelection().getSelectedElement();
            if (element && element.getName() === "input" && (acceptedTypes[ element.getAttribute('class') ] || !element.getAttribute('type'))) {
                this.cdxdtpicker = element;
                this.setupContent(element);
            }
        },
        onOk: function () {
            var editor = this.getParentEditor(),
                    element = this.cdxdtpicker,
                    fieldId = '',
                    widget_json = '',
                    field_name = this.getValueOf('info', 'title'), //控件名称
                    field_type = this.getValueOf('info', 'type'), //日期类型
                    width = this.getValueOf('info', 'width'),
                    defaultCurTime = this.getValueOf('info', 'defaultCurTime'),
                    format = '',
                    isInsertMode = !element;
            if (isInsertMode) {
                element = editor.document.createElement('input');
            }
            element.setAttribute('class', field_type);
            element.setAttribute('title', field_name);
            $(element.$).removeAttr('style');
                $(element.$).css({
                "width": width ? width : 160 + "px"
                });
            if (field_type === 'custom') {
                format = $('#custom').val();
            } else {
                format = acceptedTypes[this.getValueOf('info', 'type')];
            }
            element.setAttribute('format', format);
            element.setAttribute('defaultcurtime', defaultCurTime);
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
                    + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"class":' + '"' + field_type + '",'
                    + '"width":' + '"' + width + '",' + '"format":' + '"' + format + '",' + '"defaultCurTime":' + '"'+ defaultCurTime +'"' + '}}';
            var data = {
                element: element
            };

            if (isInsertMode) {
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
                if (fieldId === 'repeat') {
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                } else {
                    element.setAttribute('name', 'data_' + fieldId);
                    element.setAttribute('id', 'data_' + fieldId);
                    editor.insertElement(data.element);
                    this.commitContent(data);
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
                if (fieldId === 'repeat') {
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                } else {
                    editor.getSelection().selectElement(data.element);
                }
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
                        validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.common.validateControlFailed),
                        labelLayout: 'horizontal',
                        'default': '',
                        accessKey: 'N',
                        setup: function (element) {
                            this.setValue(element.getAttribute('title') || '');
                        }
                    },
                    {
                        id: 'type',
                        type: 'select',
                        label: editor.lang.common.value,
                        labelLayout: 'horizontal',
                        'default': 'date',
                        accessKey: 'M',
                        items: [
                            [editor.lang.tdforms.cdxdtpicker.date, 'date'],
                            [editor.lang.tdforms.cdxdtpicker.datetime, 'datetime'],
                            [editor.lang.tdforms.cdxdtpicker.time, 'custom']
                        ],
                        onShow: function () {
                            var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                            if (element)
                                this.setValue(element.getAttribute('class'));
                        },
                        setup: function (element) {
                            this.setValue(element.getAttribute('class'));
                        },
                        onChange: function () {
                            if ($("#wrap").length > 0) {
                                $("#wrap").remove();
                            }
                            var dialog = this.getDialog(),
                                    dateType = dialog.getValueOf('info', 'type'),
                                    thisObj = dialog.getContentElement('info', 'type');
                            var selectR = dialog.getParentEditor().getSelection().getSelectedElement();
                            var custom = selectR ? selectR.getAttribute('class') : '';
                            var customV = '';
                            if (custom === 'custom') {
                                customV = selectR.getAttribute('format');
                            }
                            if (dateType === 'custom') {
                                var str = "<div id='wrap'><br><input type='text' name='custom' id='custom' class='cke_dialog_ui_input_text' value='" + customV + "'></input></div>";
                                $("#" + thisObj.domId).find("select").after(str);
                            } else {
                                if ($("#wrap").length > 0) {
                                    $("#wrap").remove();
                                }
                            }
                        }
                    },
                    {
                        id: 'width',
                        type: 'text',
                        labelLayout: 'horizontal',
                        label: editor.lang.tdforms.tdtextfield.width,
                        'default': '',
                        accessKey: 'M',
                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                        setup: function (element) {
                            this.setValue(element.$.style.width.substr(0, element.$.style.width.length - 2));
                        }
                    },
                    {
                        type:'hbox',
                        children:[
                        {
                            type: 'html',
                            html: '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.tdforms.tdtextfield.defaultCurTime ) + '</span>'
                        },
                        {
                            id:'defaultCurTime',
                            type: 'checkbox',
                            label: '',
                            labelLayout: 'horizontal',
                            'default': '',
                            accessKey: 'M',
                            value: "checked",
                            setup: function(element){
                                this.setValue( element.getAttribute( 'defaultcurtime' ) == "true" ?  'checked' : '' );
                            }
                        }
                        ]
                    },
                    {
                        type: 'html',
                        html: "<div><tr class='cke_dialog_ui_hbox'><td>说明：<br>日历控件选择的日期、<br>时间将回填到该输入框中，<br>自定义格式详见《工作流使用详解》</td></tr></div>"
                    }
                ]
            }
        ]
    };
});
