/*
 * 表单预览及表单数据保存js
 */
formUtils = {
    listtableid: '',
    formSaveUrl: '',
    getNumUrl: '',
    modifyNumUrl: '',
    showListDataUrl: '',
    autoDocHeaderUrl: '',
    macroUrl:'',
    taskNum: '',
    taskPk: '',
    action: '',
    module: '',
    requiredField: [],
    accessWriteField: []
};
formUtils.init = function() {
    if (typeof window.top.onResize === 'function')
        window.top.onResize();
    if ($('#formSave').length > 0) {
        $('#formSave').live('click', formUtils.formSave);
    }
    if (formUtils.action != 'print' && $('table.listview').length > 0) {
        $('table.listview').each(function() {
            if ($(this).attr('type') == 'listview') {
                var computational = $('#copy_tr_' + $(this).attr('id')).attr('name');
                var table_id = $(this).attr('id');
                setInterval(function() {
                    formUtils.tb_cal(table_id, computational)
                }, 1000);
            }
        });
    }
    if ($('.auto').length > 0) {
        $('.auto').on('change', function() {
            if (window.parent && typeof window.parent.right_tab !== 'undefined' && window.parent.right_tab === 'tab_doc_frame') {
                if (typeof window.parent.frames['tab_doc_frame'].ntkoObj !== 'undefined' && window.parent.frames['tab_doc_frame'].ntkoObj.docOpen && window.parent.frames['tab_doc_frame'].ntkoObj.op == 4) {
                    var codeId = $(this).find('option:selected').attr('id');
                    if(!codeId) {
                        return false;
                    }
                    window.confirm('确认要进行文件套红？', function(ret) {
                        if (ret == true) {
                            formUtils.formSave(false);
                            var extUrl = formUtils.autoDocHeaderUrl;
                            extUrl = extUrl.replace('_docId', codeId);
                            extUrl = extUrl.replace('auto', 'ext');
                            var url = formUtils.autoDocHeaderUrl;
                            url = url.replace('_docId', codeId);
                            $.ajax({
                                url: extUrl,
                                dataType: 'json',
                                success: function(data) {
                                    if(typeof data.error != 'undefined') {
                                        _originalAlert(data.error);
                                    } else {
                                        window.parent.frames['tab_doc_frame'].ntkoObj.fileName = data.fileName;
                                        window.parent.frames['tab_doc_frame'].NTKO.autoDocHeader(url, 2, data.ext);
                                    }
                                }
                            });
                        }
                    });
                } else {
                    _originalAlert('检测到正文未正常打开！');
                }
            }
        })
    }
    if($('input[data-type="macro"]').length > 0) {
        $('input[data-type="macro"]').each(function(){
            var obj = $(this);
            $(this).mouseover(function(){
                obj.next().show();
            });
        });
    }
}
formUtils.initCalendar = function() {
    //	$('i[class=icon-calendar]').each(function() {
    //		var dateType = $(this).parent().next().attr('class');
    //		dateType = dateType.split(' ')[0];//取第一个class
    //		var format = '',formatType = '';
    //		switch(dateType) {
    //			case 'date':
    //			case 'cur_date':
    //			case 'appoint_date':
    //				formatType = 'd';
    //				format = 'yy-mm-dd';
    //				break;
    //			case 'datetime':
    //			case 'cur_time':
    //			case 'appoint_time':
    //				formatType = 'dt';
    //				format = 'yy-mm-dd hh:mm:ss';//TODO...
    //				break;
    //			case 'year_long':
    //				format = 'yy';
    //				formatType = 'd';
    //				break;
    //			case 'year_short':
    //				format = 'y';
    //				formatType = 'd';
    //				break;
    //			case 'month_0':
    //				format = 'mm';
    //				formatType = 'd';
    //				break;
    //			case 'month':
    //				format = 'm';
    //				formatType = 'd';
    //				break;
    //			default:
    //
    //		}
    //		var jsonFormat = {dateFormat:format};
    //		if(formatType == 'dt')
    //			$(this).parent().next().datetimepicker({timeFormat:'HH:mm:ss',dateFormat:'yy-mm-dd',showHour:true,showMinute:true,showTime:true});
    //		if(formatType == 'd')
    //			$(this).parent().next().datepicker(jsonFormat);
    //	});
    var format = 'yy-mm-dd';
    var jsonFormat = {
        dateFormat: format
    };
    $('input[data-type="date"]').each(function() {
        $(this).datepicker(jsonFormat);
    });
};
formUtils.formSave = function(refresh, callback) {
    var b = true;
    if ($('#document-detail-form').length > 0) {
        $('#document-detail-form').ajaxForm({
            beforeSend: function() {
                if ($('#Document_begin_dept_em_').length > 0)
                    $('#Document_begin_dept_em_').remove();
                if ($('#Document_doc_num_em_').length > 0)
                    $('#Document_doc_num_em_').remove();
                if (!$.trim($('#Document_doc_num').val())) {
                    $('#Document_doc_num').after('<span class="help-inline error" id="Document_doc_num_em_" style="">发文字号 不能为空.</span>')
                    return false;
                }
                if (!$.trim($('#Document_begin_dept').val())) {
                    $('#Document_begin_dept').after('<span class="help-inline error" id="Document_begin_dept_em_" style="">发文部门 不能为空.</span>')
                    return false;
                }
            },
            complete: function(data) {
                if (data.responseText == 'OK') {
                    $.notify({
                        type: 'success',
                        message: {
                            text: '表单保存成功！',
                            icon: 'icon-info'
                        }
                    }).show();
                    if ($.type(callback) === 'function') {
                        callback(1);
                    }
                    if (refresh === true) {
                        window.location.reload();
                    }
                } else {
                    $.notify({
                        type: 'error',
                        message: {
                            text: '表单保存失败！',
                            icon: 'icon-info'
                        }
                    }).show();
                    if ($.type(callback) === 'function') {
                        callback(-1);
                    }
                }
            }
        });
        $('#document-detail-form').submit();
    } else {
        if ($('#DWebSignSeal').length > 0) {
            Websign.beforeSubmit();
        }
        var formID = $('#formID').val();
        var formObj = $('#' + formID + '-form');
        var formData = formUtils.gatherData();
        var hasError = formUtils.validateField();
        if (hasError['hasError']) {
            _originalAlert('必填字段[' + hasError['fieldName'] + ']不能为空！');
            if ($.type(callback) === 'function') {
                callback(-1);
            }
            return false;
        }
        $('#formData').val(formData);
        $('#taskPk').val(this.taskPk);
        formObj.ajaxForm({
            async:false,//兼容处理表单数据到正文书签映射
            complete: function(data) {
                var items = $.parseJSON(data.responseText);
                $('#formData').val('');
                if (items && items.result === 'OK') {
                    $.notify({
                        type: 'success',
                        message: {
                            text: '表单数据保存成功',
                            icon: 'icon-info'
                        }
                    }).show();
                    if (window.parent && typeof window.parent.right_tab !== 'undefined' && window.parent.right_tab === 'tab_doc_frame') {
                        if (typeof window.parent.frames['tab_doc_frame'].ntkoObj !== 'undefined' && window.parent.frames['tab_doc_frame'].ntkoObj.docOpen && window.parent.frames['tab_doc_frame'].ntkoObj.op == 4) {
                            window.parent.frames['tab_doc_frame'].NTKO.addFlowData();
                        }
                    }
                    b = true;
                    $.each(items, function(key, value) {
                        if (key !== 'result' && $('#' + key).length > 0) {
                            $('#' + key + ' div[is_update="yes"]').attr('keyid', value);
                        }
                    });
                    if ($.type(callback) === 'function') {
                        callback(1);
                    }
                    if (refresh === true) {
                        window.location.reload();
                    }
                }
            }
        });
        $('#' + formID + '-form').attr('action', formUtils.formSaveUrl);
        formObj.submit();
    }
    return b;
}
formUtils.validateField = function() {
    var requiredFields = formUtils.requiredField;
    var hasError = false;
    var fieldName = '';
    for (var i in requiredFields) {
        switch (requiredFields[i]) {
            case 'v_text':
            case 'v_int':
            case 'v_float':
            case 'v_date':
            case 'v_email':
            case 'date':
            case 'datetime':
            case 'custom':
            case 'macro':
            case 'tcount':
            case 'select':
                if ($('#data_' + i).val() == '') {
                    $('#data_' + i).focus();
                    fieldName = $('#data_' + i).attr('title');
                    hasError = true;
                }
                break;
            case 'user':
            case 'dept':
                if ($('#data_' + i).val() == '') {
                    $('#data_' + i + '_name').focus();
                    fieldName = $('#data_' + i + '_name').attr('title');
                    hasError = true;
                }
                break;
            case 'checkbox':
                if ($('#data_' + i).is(':checked') == false) {
                    $('#data_' + i).focus();
                    fieldName = $('#data_' + i).attr('title');
                    hasError = true;
                }
                break;
            case 'label':
                hasError = true;
                $('#data_' + i + ' input').each(function() {
                    if ($(this).is(':checked')) {
                        hasError = false;
                    }
                });
                if (hasError)
                    fieldName = $('#data_' + i).attr('title');
                break;
            case 'textarea':
                if ($('#data_' + i).val() == '') {
                    $('#data_' + i).focus();
                    fieldName = $('#data_' + i).attr('title');
                    hasError = true;
                }
                break;
            case 'listview':
                break;
            case 'countersign':
                if ($('#data_' + i + ' div[is_update="yes"]').find('textarea').val() == '') {
                    $('#data_' + i + ' div[is_update="yes"]').find('textarea').focus();
                    fieldName = $('#data_' + i).attr('title');
                    hasError = true;
                }
                break;
            case 'signature':
                break;
            case 'fileupload':
                break;
        }
        if (hasError) {
            break;
        }
    }
    return {"hasError": hasError, "fieldName": fieldName};
}
formUtils.getTcount = function(obj) {
    var data = {}, keys = [], digit = '';
    var formID = $('#formID').val();
    var dataID = $('#dataID').val();
    var relations = $.parseJSON($(obj).attr('relations'));
    data.id = $(obj).attr('id');
    data.ruleId = $(obj).attr('rule_id');
    data.relations = $(obj).attr('relations');
    $.each(relations, function(key, value) {
        if (value == 'DIGIT') {
            digit = key;
            data['digitField'] = digit;
            data['dataID'] = dataID;
            data['formID'] = formID;
            return true;
        } else {
            data[value] = $('#' + key).val();
            keys.push(key);
        }
    });
    $.ajax({
        type: "post",
        url: formUtils.getNumUrl,
        data: data,
        dataType: 'json',
        success: function(data) {
            var btnId = $(obj).attr('id');
            $('#' + digit).val(data['digit']);
            $('#' + btnId + '_text').val(data['expression']);
            $('#' + btnId).val('修改');
            $('#' + btnId).removeClass('btn-primary').addClass('btn-success');
            $('#' + btnId).attr('onclick', 'formUtils.updateTcount(this)');
            $.each(keys, function(i, id) {
                $('#' + id).attr('disabled', true);
            });
        }
    });
}
formUtils.updateTcount = function(obj) {
    var formID = $('#formID').val();
    var dataID = $('#dataID').val();
    var modifyUrl = TUrlManager.parseUrl({'params': {'formID': formID, 'dataID': dataID, 'digitID': $(obj).attr('id'), 'rule': $(obj).attr('rule_id')}, 'baseUrl': formUtils.modifyNumUrl});
    TUtil.openUrl(modifyUrl, '', 'update', 450, 350);
}
formUtils.reloadMacro = function(id,type) {
    $.ajax({
        type:'get',
        url:formUtils.macroUrl,
        data:{type:type},
        success:function(v){
            $('#' + id).val(v);
        }
    });
}
formUtils.deleteTr = function(table_id, obj) {
    var trnum = ($(obj).parent().parent().find('td:first').text());
    $("#" + table_id).find("tr:gt(" + trnum + ")").each(function() {
        var nextTr = Number($(this).find('td:first').text());
        if (nextTr) {
            $(this).attr('id', 'tr_' + (nextTr - 1) + '_' + table_id);
            $(this).find('td:first').text(nextTr - 1);
        }
    });
    $(obj).parent().parent().remove();
}
formUtils.addTableRow = function(table_id) {
    var prevTrIndex = $('#' + table_id).find('tr:last').index() + 1, newId = '';
    newId = 'tr_' + prevTrIndex + '_' + table_id;
    var cloneObj = $('#copy_tr_' + table_id).clone(true);
    $(cloneObj).show();
    if ($('#total_' + table_id).length > 0) {
        newId = 'tr_' + (prevTrIndex - 1) + '_' + table_id;
        $('#total_' + table_id).before(cloneObj);
        $(cloneObj).find('td:first').text(prevTrIndex - 1).end().attr('id', newId);
    } else {
        $(cloneObj).appendTo($('#' + table_id)).find('td:first').text(prevTrIndex).end().attr('id', newId);
    }
    $('#' + newId + ' td').children().each(function() {
        if ($(this).get(0).tagName == 'LABEL') {
            $(this).find('input[type=radio]').each(function() {
                $(this).attr('name', newId + '_' + $(this).attr('name'));
            });
        } else {
            if (typeof ($(this).attr('name')) == 'undefined' || $(this).attr('name').indexOf('computational') == 0)
                return true;
            $(this).attr('name', newId + '_' + $(this).attr('name'));
        }
    });
}
formUtils.showListData = function(fieldId) {
    formUtils.listtableid = 'data_' + fieldId;
    var showListDataUrl = TUrlManager.parseUrl({'params': {'fieldID': fieldId}, 'baseUrl': formUtils.showListDataUrl});
    TUtil.openUrl(showListDataUrl, '', 'showListData', 850, 350);
}
formUtils.insertListData = function(name, value) {
    $('#' + formUtils.listtableid + ' tr:last').find('td').each(function() {
        if ($(this).children().length == 0) {
            return true;
        }
        $(this).children().each(function() {
            var label = $(this).get(0).tagName;
            if (label === 'LABEL') {
                $(this).find('input[type=radio]').each(function() {
                    if ($(this).attr('field') === name && $(this).val() === value) {
                        $(this).attr('checked', true);
                    }
                });
            } else {
                if ($(this).attr('field') === name) {
                    if (label === 'INPUT' && $(this).attr('type') === 'checkbox') {
                        $(this).attr('checked', value ? true : false);
                    } else if (label === 'TEXTAREA') {
                        $(this).val(value);
                        $(this).text(value);
                    } else {
                        $(this).val(value);
                    }
                }
            }
        });
    });
}
formUtils.writeSignature = function(obj) {
    var item_id = $(obj).parent().attr('id');
    var bind_id = $(obj).attr('bind_data');
    var bind_arr = bind_id.split(",");
    var bind_data = "{";
    for (var i = 0; i < bind_arr.length; i++) {
        var real_id = "data_" + bind_arr[i];
        var _tagName = $('#' + real_id).get(0).tagName,
                bind_value = $('#' + real_id).val(),
                $this = $('#' + real_id);
        if (_tagName == 'LABEL') {
            bind_value = $this.find('input:checked').val();
        } else if (_tagName == 'TABLE' && $this.attr('class') == 'listview') {
            bind_value = gatherListTableData(real_id);
        } else if (_tagName == 'INPUT') {
            if ($this.attr('type') == 'checkbox') {
                bind_value = typeof ($this.attr('checked')) == 'undefined' ? '' : 'checked';
            }
        }
        bind_data += '"' + real_id + '":' + '"' + bind_value + '",';
    }
    bind_data = bind_data.substr(bind_data.length - 1) == ',' ? bind_data.substr(0, bind_data.length - 1) : bind_data;
    bind_data += "}";

    bind_data = String(bind_data);
    var tmp_data_id = String(data_id);
    window.Android.WriteSignature(form_id, tmp_data_id, item_id, bind_data);
}
//组织表单保存数据
formUtils.gatherData = function() {
    var accessWriteField = formUtils.accessWriteField;
    var module = formUtils.module;
    var dataJson = '{';
    $('[id^=data_]').each(function() {
        var fieldId = $(this).attr('id');
        var idArray = fieldId.split('_');
        if (idArray.length != 2 || !idArray[1].match('^[0-9a-z]{13}$')) {
            return true;
        }
        if (($.inArray(idArray[1], accessWriteField) >= 0)  || ((module!="document") && (module!="workflow"))) {
            var tagName = $(this).get(0).tagName.toLowerCase(),
                    type = tagName, isObj = false,
                    value = $(this).val(), name = $(this).attr('title');
            if(value) {
                value = value.replace(/\"/g, "\\\"");
            }
            if (tagName == 'label') {
                value = $(this).find('input:checked').val();
                value = value == undefined ? "" : value;
            } else if (tagName == 'table' && typeof ($(this).attr('class') != 'undefined')) {
                isObj = true;
                type = $(this).attr('type');
                if (type == 'listview') {
                    value = formUtils.gatherListTableData(fieldId);
                }
            } else if (tagName == 'div' && typeof ($(this).attr('class') != 'undefined')) {
                type = $(this).attr('class');
                if (type == 'countersign') {
                    isObj = true;
                    value = formUtils.gatherCountersignData(fieldId);
                } else if (type == 'signature') {
                    value = $(this).find('input:first').attr('bind_data');
                } else if (type == 'writesign') {
                    value = $(this).find('#SIGN_POS_' + fieldId).attr('bind_data');
                }
            } else if (tagName == 'img' && ($(this).attr('class') == 'dimensionalcode')) {
                type = 'dimensionalcode';
                value = formUtils.gatherQrcode($(this).attr('value'));
                isObj = true;
            } else if (tagName == 'input') {
                if ($(this).attr('sign_type') == 'handwrite' || $(this).attr('sign_type') == 'addseal') {
                    value = $(this).parent().attr('bind_data');
                } else {
                    if ($(this).attr('type') == 'checkbox') {
                        type = 'checkbox';
                        value = typeof ($(this).attr('checked')) == 'undefined' ? 'unchecked' : 'checked';
                    } else if ($(this).attr('type') == 'hidden') {
                        if ($(this).attr('data-type') == 'formupload') {
                            type = 'formupload';
                        } else if($(this).attr('class') == 'tcount'){
                            type = 'tcount';
                        } else if ($('#' + fieldId + '_name').length > 0 && ($('#' + fieldId + '_name').attr('class') == 'user' || $('#' + fieldId + '_name').attr('class') == 'org')) {
                            type = $('#' + fieldId + '_name').attr('class');
                            value = $('#' + fieldId + '_name').val();
                        }
                    } else {
                        type = $(this).attr('class');
                        if (typeof ($(this).attr('relations')) != 'undefined') {
                            value = $('#' + fieldId + '_text').val();
                            type = 'tcount';
                        }
                    }
                }
            }
            if (value == '') {
                dataJson += '"' + fieldId + '"' + ':' + '{"name":' + '"' + name + '",' + '"type":' + '"' + type + '",' + '"value":' + (isObj == false ? '"' + value + '"' : value) + '}' + ',';
            } else {
                dataJson += '"' + fieldId + '"' + ':' + '{"name":' + '"' + name + '",' + '"type":' + '"' + type + '",' + '"value":' + (isObj == false ? '"' + value + '"' : value) + '}' + ',';
            }
        }
    });
    dataJson = dataJson.substr(dataJson.length - 1) == ',' ? dataJson.substr(0, dataJson.length - 1) : dataJson;
    dataJson += '}';
    return dataJson;
}
//二维码绑定数据
formUtils.gatherQrcode = function(bindField) {
    var bindFieldArr = Array(), fieldTitle = '';
    if (bindField) {
        bindFieldArr = bindField.split(',');
        var bind_data = "{";
        for (var i = 0; i < bindFieldArr.length; i++) {
            if (bindFieldArr[i] != ',') {
                var dataID = "data_" + bindFieldArr[i];
                var $this = $('#' + dataID);
                var _tagName = $this.get(0).tagName.toLowerCase(),
                        bind_value = $this.val();
                if (_tagName == 'label') {
                    bind_value = $this.find('input:checked').val();
                } else if (_tagName == 'table' && $this.attr('class') == 'listview') {
                    bind_value = gatherListTableData(dataID);
                } else if (_tagName == 'input') {
                    if ($this.attr('type') == 'checkbox') {
                        bind_value = typeof ($this.attr('checked')) == 'undefined' ? '' : 'checked';
                    }
                }
                bind_data += '"' + dataID + '":' + '"' + bind_value + '",';
            }
        }
        bind_data = bind_data.substr(bind_data.length - 1) == ',' ? bind_data.substr(0, bind_data.length - 1) : bind_data;
        bind_data += "}";
    }
    return bind_data;
}
formUtils.gatherCountersignData = function(id) {//只取可写字段值
    var defaultValue = '{' + '"countersign":';
    var keyfield = $('#' + id).attr('key_field');
    var isUpdate = false;
    var i=0;
    $('#' + id).children().each(function() {
        if (($(this).attr('is_update') == 'yes') && (i==0)) {
            i++;
            isUpdate = true;
            var keyid = $(this).attr('keyid') == '' ? "" : $(this).attr('keyid');
            var signOrder = $(this).attr('signorder') == '' ? 1 : $(this).attr('signorder');
            var formDataId = $('#dataID').val();
            defaultValue += '{'
            $(this).find('textarea,span').each(function() {
                var signValue = $(this).get(0).tagName.toLowerCase() == 'textarea' ? $(this).val() : $(this).text();
                if ($(this).attr('name') == 'signcontent') {
                    if(signValue) {
                        signValue = signValue.replace(/\"/g, "\\\"");
                    }
                    defaultValue += '"signcontent":' + '"' + signValue + '",';
                }
                if ($(this).attr('name') == 'signuser') {
                    defaultValue += '"signuser":' + '"' + signValue + '",';
                }
                if($(this).attr('name') == 'signorg'){
                    defaultValue += '"signorg":' + '"' + signValue + '",';
                }
                if ($(this).attr('name') == 'signtime') {
                    defaultValue += '"signtime":' + '"' + signValue + '",';
                }
            });
            var signData = '';
            var signObj = $('div[fieldid="' + id + '"]');
            if (signObj.length > 0) {
                signData = signObj.attr('bind_data');
            }
            var userId = $('#' + id).find('input[type="hidden"]').val();
            defaultValue += '"user_id":' + '"' + userId + '",';
            defaultValue += '"field_id":' + '"' + keyfield + '",';
            defaultValue += '"form_data_id":' + '"' + formDataId + '",';
            defaultValue += '"id":' + '"' + keyid + '",';
            defaultValue += '"signdata":' + '"' + signData + '",';
            defaultValue += '"task_id":' + '"' + formUtils.taskPk + '",';
            defaultValue += '"signorder":' + '"' + signOrder + '"';
            defaultValue += '}}';
            return true;
        }
    });
    if (!isUpdate) {
         defaultValue += '"' + keyfield + '"}';
    }
    return defaultValue;
}
//列表数据
formUtils.gatherListTableData = function(table_id) {
    var keyfield = $('#' + table_id).attr('keyfield');
    var trId = '', listData = '{' + '"' + keyfield + '":' + '{';
    if ($('#' + table_id + ' tbody tr').length > 1) {
        $('#' + table_id + ' tr[id^=tr_]').each(function() {
            var tdsInfo = '{', count = 0;
            trId = $(this).attr('id');
            if (trId == 'tr_0_' + table_id)
                return true;
            $(this).find('td').each(function() {
                if ($(this).children().length == 0)
                    return true;
                count++;
                var value = '', tagName;
                $(this).children().each(function() {
                    var tdId = typeof ($(this).attr('field')) == 'undefined' ? "td_" + count : $(this).attr('field');
                    tagName = $(this).get(0).tagName.toLowerCase();
                    var type = tagName;
                    var name = typeof ($(this).attr('name')) == 'undefined' ? '' : $(this).attr('name');
                    value = $(this).val();
                    if (tagName == 'select') {
                        value = $(this).find('option:selected').val();
                    }
                    if (tagName == 'input') {
                        if ($(this).attr('type') == 'checkbox') {
                            type = 'checkbox';
                            value = typeof ($(this).attr('checked')) == 'undefined' ? 0 : 1;
                        } else if ($(this).attr('type') == 'button') {
                            return true;
                            type = 'button';
                        }
                    }
                    if (tagName == 'label') {
                        $(this).find('input').each(function() {
                            tdId = $(this).attr('field');
                            name = $(this).attr('name');
                        });
                        value = typeof ($(this).find('input:checked').val()) == 'undefined' ? 0 : $(this).find('input:checked').val();
                        type = 'radio';
                    }
                    tdsInfo += '"' + tdId + '"' + ':' + '{' + '"type":' + '"' + type + '",' + '"name":' + '"' + name + '",' + '"value":' + '"' + value + '"' + '}' + ',';
                });
            });
            tdsInfo = tdsInfo.substr(tdsInfo.length - 1) == ',' ? tdsInfo.substr(0, tdsInfo.length - 1) : tdsInfo;
            tdsInfo += '}';
            listData += '"' + trId + '"' + ':' + tdsInfo + ',';
        });
        if ($('#total_' + table_id).length > 0)
            listData += '"total":' + '{' + '"hasTotal":"yes",' + '"total":' + '"' + $('#total_text_' + table_id).val() + '"}';
        else
            listData += '"total":' + '{' + '"hasTotal":"no"}';
        listData = listData.substr(listData.length - 1) == ',' ? listData.substr(0, listData.length - 1) : listData;
    }
    listData += '}}';
    return typeof (listData) == 'undefined' ? '' : listData;
}
formUtils.tb_cal = function(table_id, cal) {
    var cell_value, $table = $('#' + table_id), total = $('#total_' + table_id).length;
    if (!((total > 0 && $table.find('tr').length > 3)
            || (total == 0 && $table.find('tr').length > 2))) {
        return;
    }
    if (cal.indexOf('[') != -1) {
        var cal_array = cal.split(",");
        for (var i = 1; i < cal_array.length - 1; i++) {
            if (cal_array[i] != '')
                var cal_str = cal_array[i];
        }
        $table.find('tr[id^=tr_]').each(function() {
            if ($(this).attr('id') == 'tr_0')
                return true;
            for (i = 1; i < cal_array.length - 1; i++) {
                var cal_str = cal_array[i];
                if (cal_str == "")
                    continue;
                $(this).find('td').each(function() {
                    var re = new RegExp("\\[" + $(this).index() + "\\]", "ig");
                    cell_value = parseFloat($(this).find(':first-child').val());
                    if ($(this).find(':first-child').val()) {
                        var sss = $(this).find(':first-child').val().indexOf("%");
                        if (sss > -1) { //处理%
                            cell_value = cell_value / 100;
                        }
                    }
                    if (isNaN(cell_value))
                        cell_value = 0;
                    cal_str = cal_str.replace(re, cell_value);
                });
                $(this).find('input[name^=computational]').val(isNaN(eval(cal_str)) ? 0 : Math.round(parseFloat(eval(cal_str)) * 10000) / 10000);
            }
        });
        if (total > 0) {
            var tos = 0;
            $('#' + table_id + ' input[name^=computational]').each(function() {
                tos += Number($(this).val());
            });
            $('#total_text_' + table_id).val(tos);
        }
    }
}
function selectOtherOrg(id, type) {
    if (formUtils.isObjExists($('#' + id))) {
        var availWidth = screen.availWidth;
        var availHeight = screen.availHeight;
        var myWidth = availWidth / 2.5;
        var myHeight = availHeight / 2;
        var myLeft = (availWidth - myWidth) / 2;
        var myTop = (availHeight - myHeight) / 2;
        var codeUrl = TUrlManager.parseUrl({'params': {'id': id, 'type': type}, 'baseUrl': formUtils.sysCodeUrl})
        TUtil.openUrl(codeUrl, '', 'selectOtherOrg', myWidth, myHeight, myLeft, myTop);
    }
}
function delOtherOrg(id) {
    if (formUtils.isObjExists($('#' + id))) {
        $('#' + id).val('');
}
}
formUtils.isObjExists = function(obj) {
    return obj.length > 0;
}
$(document).ready(function() {
    $('.listview  select').change(function(){
        $(this).attr('title',$(this).val());
    })
})