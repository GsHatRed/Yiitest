CKEDITOR.dialog.add('listview', function (editor) {
    return {
        title: editor.lang.tdforms.listview.dialogTitle,
        minWidth: 850,
        minHeight: 350,
        onShow: function () {
            delete this.listview;
            var element = this.getParentEditor().getSelection().getSelectedElement();
            if (element && element.getName() === "img" && element.getAttribute('class') === 'listview') {
                this.listview = element;
                this.setupContent(element);
            }
        },
        onOk: function () {
            var editor, _trstr = '',
                    listviewObj = $(window.frames["listiframe"].document),
                    element = this.listview,
                    fieldId = '',
                    widget_json = '',
                    field_type = 'listview',
                    field_attr = '',
                    field_name = listviewObj.find('#listviewtitle').val(), //this.getValueOf('info', 'title'),
                    data_src = listviewObj.find('#datasrc').val(),
                    isInsertMode = !element;
            if (isInsertMode) {
                editor = this.getParentEditor();
                element = editor.document.createElement('img');
                element.setAttribute('src', baseUrl + '/images/form/listview.png');
                element.setAttribute('class', 'listview');
                element.setAttribute('title', field_name);
            }
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":"listview",';
            var fieldName = [], isEmpty = false;
            field_attr += '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"datasrc":' + '"' + data_src + '",' + '"type":' + '"' + field_type + '",' + '"class":"listview",' + '"value":' + '{';
            var hasdatasrc = listviewObj.find('#has_data_src').val();
            listviewObj.find("#custom_table tr:gt(0)").each(function () {
                var _index = $.trim($(this).find('input[name=fieldname]').val());//'tr_' + $(this).index();
                var serialnumber = $(this).find('td:first').text();
                var headerfield = $(this).find('input[name=headerfield]').val();
                var fieldname = $.trim($(this).find('input[name=fieldname]').val());
                if (!fieldname) {
                    _originalAlert('唯一标识不能为空');
                    $(this).find('input[name=fieldname]').focus();
                    isEmpty = true;
                    return false;
                } else if (!(fieldname.match('^[a-z_]{1,64}$'))) {
                    _originalAlert('唯一标识必须由小写字母或下划线组成！');
                    $(this).find('input[name=fieldname]').focus();
                    isEmpty = true;
                    return false;
                } else {
                    fieldName.push(fieldname);
                }
                var fieldwidth = $(this).find('input[name=fieldwidth]').val();
                var total = $(this).find('input[name=total]').attr('checked') == undefined ? 0 : 1;
                var computational = $(this).find('input[name=computational]').val();
                var fieldtype = $(this).find('select[name=fieldtype]').val();
                var defaultvalue = $(this).find('input[name=defaultvalue]').val();
                if (hasdatasrc) {
                    var datasrcfield = $(this).find('select[name=datasrcfield]').val();
                    var searchfield = $(this).find('input[name=searchfield]').attr('checked') == undefined ? 0 : 1;
                }
                _trstr += '"' + _index + '"' + ":" + "{" + '"serialnumber":' + '"' + serialnumber + '",' + '"headerfield":' + '"' + headerfield + '",'
                        + '"fieldname":' + '"' + fieldname + '",' + '"fieldwidth":' + '"' + fieldwidth + '",' + '"total":' + '"' + total + '",' + '"computational":' + '"' + computational + '",'
                        + '"fieldtype":' + '"' + fieldtype + '",' + '"defaultvalue":' + '"' + defaultvalue;
                if (datasrcfield) {
                    _trstr += '",' + '"datasrcfield":' + '"' + datasrcfield + '",' + '"searchfield":' + '"' + searchfield;
                }
                _trstr += '"' + "},";
            });
            _trstr = _trstr.substr(_trstr.length - 1) === ',' ? _trstr.substr(0, _trstr.length - 1) : _trstr;
            _trstr += '}';
            field_attr += _trstr + '}';
            widget_json += field_attr + '}';
            if (isEmpty)
                return false;
            if (!($.isEmptyObject(fieldName))) {
                var hash = {};
                for (var i in fieldName) {
                    if (!hash[fieldName[i]]) {
                        hash[fieldName[i]] = true;
                    } else {
                        _originalAlert('存在重复唯一标识 ' + fieldName[i]);
                        return false;
                    }
                }
            }
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
                    element.setAttribute('name', "data_" + fieldId);
                    element.setAttribute('id', 'data_' + fieldId);
                }
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
                if (fieldId === 'repeat') {
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                }
            }
            element.setAttribute('data-type', field_type);
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
                            var a = g_loadListViewURL;
                            var b = a.replace('_formID', g_form_id);
                            b = b.replace('_randomNum', Math.floor(Math.random() * 100000));
                            if (id && eclass === 'listview') {
                                b = b.replace('_id', id);
                            } else {
                                b = b.replace('_id', 'new');
                            }
                            $("#listiframe").attr("src", b);
                        },
                        type: 'html',
                        html: '<iframe style="width:1080px;height:420px" id="listiframe" name="listiframe" src=""></iframe>'
                    }
                ]
            }
        ]
    }
});