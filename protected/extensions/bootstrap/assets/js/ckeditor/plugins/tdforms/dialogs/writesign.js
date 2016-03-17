/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
        CKEDITOR.dialog.add('writesign', function (editor) {
            return {
                title: editor.lang.tdforms.writesign.dialogTitle,
                minWidth: 350,
                minHeight: 220,
                onShow: function () {
                    delete this.writesign;
                    var element = this.getParentEditor().getSelection().getSelectedElement();
                    if (element && element.getName() === "img" && element.getAttribute('class') === 'writesign') {
                        this.writesign = element;
                        this.setupContent(element);
                    }
                },
                onOk: function () {
                    var editor,
                            writesignObj = $(window.frames["writesign"].document),
                            element = this.writesign,
                            fieldId = '',
                            widget_json = '',
                            field_type = 'writesign',
                            field_name = writesignObj.find('#writesigntitle').val(),
                            handwrite = writesignObj.find('#handwrite').is(':checked'),
                            addseal = writesignObj.find('#addseal').is(':checked'),
                            handwritepop = writesignObj.find('#handwritepop').is(':checked'),
                            left = writesignObj.find('#left').val(),
                            top = writesignObj.find('#top').val(),
                            writecolor = writesignObj.find('#writecolor').val()
                    isInsertMode = !element,
                            dv = "";
                    if(!TUtil.trim(field_name)) {
                        _originalAlert('控件名称不能为空！');
                        return false;                        
                    }                    
                    writesignObj.find(".ms-selection .ms-selected").each(function () {
                        var id = $(this).attr('id').replace(/_/g, '').split('-')[0];
                        dv += id + ',';
                    });
                    dv = dv.substr(dv.length - 1) === ',' ? dv.substr(0, dv.length - 1) : dv;
                    widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
                            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"handwrite":' + '"' + handwrite + '",' + '"addseal":' + '"' + addseal + '",' + '"handwritepop":' + '"' + handwritepop + '",' + '"left":' + '"' + left + '",' + '"top":' + '"' + top + '",' + '"class":' + '"' + field_type + '",'
                            + '"value":' + '"' + dv + '",' + '"writecolor":' + '"' + writecolor + '"' + '}}';
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
                            editor = this.getParentEditor();
                            element = editor.document.createElement('img');
                            element.setAttribute('src', baseUrl + '/images/form/writesign.png');
                            element.setAttribute('class', 'writesign');
                            element.setAttribute('title', field_name);
                            element.setAttribute('writecolor', writecolor);
                            element.setAttribute('value', dv);
                            element.setAttribute('name', "data_" + fieldId);
                            element.setAttribute('id', 'data_' + fieldId);
                        }
                    }


                    if (!isInsertMode) {
                        element.setAttribute('value', dv);
                        element.setAttribute('writecolor', writecolor);
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
                        }
                    }
                    element.setAttribute('data_left', left);
                    element.setAttribute('data_top', top);
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
                                onShow: function () {
                                    var selement = editor.getSelection().getSelectedElement();
                                    var eclass = selement ? selement.getAttribute('class') : '';
                                    id = selement ? selement.getAttribute('name').split('_')[1] : '';
                                    var a = g_loadWebSignURL;
                                    var b = a.replace('_formID', g_form_id);
                                    b = b.replace('_randomNum', Math.floor(Math.random() * 100000));
                                    if (id && eclass === 'writesign') {
                                        b = b.replace('_id', id);
                                    } else {
                                        b = b.replace('_id', 'new');
                                    }
                                    $("#writesign").attr("src", b);
                                },
                                type: 'html',
                                html: '<iframe style="width:510px;height:450px" id="writesign" name="writesign" src=""></iframe>'
                            }
                        ]
                    }
                ]
            };
        });