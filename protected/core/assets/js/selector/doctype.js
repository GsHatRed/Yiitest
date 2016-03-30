var VKDocType = {
    baseUrl: null,
    block: null, //action
    treeNode: null,
    single: false,
    urlParams: {},
    init: function (hiddenId, typeName, modalId, single) {
        this.hiddenId = $('#' + hiddenId);
        this.typeName = $('#' + typeName);
        this.modalId = $('#' + modalId);
        this.single = (single == 1 ? true : false);
    },
    load: function () {
        VKDocType.bindClick();
        $('#vk_form_tab_sort').show();
    },
    bindClick: function () {
        //右侧列表点击
        $(document).on('click', '#vk_doctype_item tbody tr', function () {
            if (typeof ($(this).attr('item_id')) != 'undefined') {
                $(this).toggleClass('form_selected');
                if ($(this).hasClass('form_selected')) {
                    VKDocType.add($(this).attr('item_id'), $(this).attr('item_name'));
                } else {
                    VKDocType.del($(this).attr('item_id'), $(this).attr('item_name'));
                }
            }
        });
        $('#selectdoctype .modal-footer a').on('click', function () {
            $('#selectdoctype').modal('hide');
        });
        $('#vk_doctype_item i[name="checkStatu"]').live('click', function () {
            var curClass = $(this).attr('class');
            if (curClass === 'icon-checkbox-unchecked') {
                if ($('#vk_doctype_item tbody tr').length > 0) {
                    $('#vk_doctype_item tbody tr').each(function () {
                        if (typeof ($(this).attr('item_id')) != 'undefined') {
                            $(this).addClass('form_selected');
                            VKDocType.add($(this).attr('item_id'), $(this).attr('item_name'));
                        }
                    });
                }
                $(this).removeClass(curClass).addClass('icon-checkbox-checked');
                $(this).attr('data-original-title', '全部取消');
            } else {
                if ($('#vk_doctype_item tbody tr[class="form_selected"]').length > 0) {
                    $('#vk_doctype_item tbody tr[class="form_selected"]').each(function () {
                        $(this).removeClass('form_selected');
                        VKDocType.del($(this).attr('item_id'), $(this).attr('item_name'));
                    });
                }
                $(this).removeClass(curClass).addClass('icon-checkbox-unchecked');
                $(this).attr('data-original-title', '全选');
            }
        });
    }
    //左侧部门点击事件
    ,
    zTreeOnClick: function (event, treeId, treeNode) {
        VKDocType.treeNode = treeNode;
        VKDocType.urlParams = {
            action: "sort",
            sortId: (treeNode.id == '' ? '0' : treeNode.id)
        };
        var btnstr = '<p class="pull-right" style="margin: 0px;"><i href="javascript:;" name="checkStatu" style="cursor:pointer;" class="icon-checkbox-unchecked" rel="tooltip" node-type="helpBtn" data-original-title="全选"> </i></p>';
        $('#curSort').html(treeNode.name + btnstr);
        VKDocType.afterClick();
        $('#vk_doctype_item tbody tr').removeClass('form_selected');
        if ((VKDocType.hiddenId.val() !== "" && VKDocType.hiddenId.val() != 0) || VKDocType.typeName.val() !== "") {
            var ids = VKDocType.hiddenId.val().split(',');
            for (i in ids) {
                if (ids[i] == ',' || ids[i] == '') {
                    continue;
                } else if ($('#vk_doctype_item tbody tr[item_id="' + ids[i] + '"]').length > 0) {
                    $('tr[item_id="' + ids[i] + '"]').addClass('form_selected');
                }
            }
        }
    },
    afterClick: function () {
        var htmlStr = VKDocType.beforeRenderHtml();
        var $item = $('#vk_doctype_item');
        if ($item.length > 0) {
            $item.find('tbody').html(htmlStr);
        }
    },
    beforeRenderHtml: function () {
        var htmlStr = '', jsonData = '';
        if (VKDocType.urlParams.hasOwnProperty('sortId')) {//click
            jsonData = VKDocType.getJsonData();
            if (!$.isEmptyObject(jsonData)) {
                htmlStr = VKDocType.getHtmlStr(jsonData);
            }
        }
        return htmlStr;
    },
    getHtmlStr: function (jsonData) {
        var htmlStr = '';
        $.each(jsonData, function (id, name) {
            if (id) {
                htmlStr += '<tr item_id="' + id + '" item_name="' + name + '" class="form_selected"><td style="text-align:center;">' + name + '</td></tr>';
            }
        });
        return htmlStr;
    },
    getJsonData: function () {
        var jsonData = '';
        $.ajax({
            type: 'POST',
            url: VKDocType.baseUrl,
            data: VKDocType.urlParams,
            dataType: 'json',
            async: false,
            success: function (data) {
                jsonData = data;
            },
            error: function (msg) {
                jsonData = false;
            }
        });
        return jsonData;
    },
    counter: function (n) {
    },
    add: function (item_id, item_name) {
        var self = this;
        var $tid = this.hiddenId;
        var $tname = this.typeName;
        if (!item_id || !item_name)
            return;
        if ($tid.val().indexOf(item_id + ",") !== 0 && $tid.val().indexOf("," + item_id + ",") <= 0) {
            $tid.attr('value', function () {
                if (!this.value || this.value.substr(this.value.length - 1) === ',') {
                    return this.value + item_id + ',';
                } else {
                    return this.value + ',' + item_id + ',';
                }
            });
            $tname.attr('value', function () {
                if (!this.value || this.value.substr(this.value.length - 1) === ',') {
                    return this.value + item_name + ',';
                } else {
                    return this.value + ',' + item_name + ',';
                }
            });
        }
    }

    ,
    del: function (item_id, item_name) {
        var $tid = this.hiddenId;
        var $tname = this.typeName;

        if (!item_id || !item_name) {
            return;
        }

        if ($tid.val().indexOf(item_id + ",") === 0 || $tid.val().indexOf("," + item_id + ",") > 0) {
            $tid.attr('value', function () {
                return this.value.replace(item_id + ',', '');
            });
            $tname.attr('value', function () {
                return this.value.replace(item_name + ',', '');
            });
        } else {
            $tid.attr('value', function () {
                return this.value.replace(item_id, '');
            });
            $tname.attr('value', function () {
                return this.value.replace(item_name, '');
            });
        }
    },
    show: function () {
        if ((this.hiddenId.val() !== "" && this.hiddenId.val() != 0) || this.typeName.val() !== "") {
            $('#vk_doctype_item tbody').empty();
            var ids = this.hiddenId.val().split(',');
            var names = this.typeName.val().split(',');
            for (i in ids) {
                if (ids[i] == ',' || ids[i] == '') {
                    continue;
                }
                $('#vk_doctype_item tbody').append('<tr item_id="' + ids[i] + '" item_name="' + names[i] + '" class="form_selected"><td style="text-align:center;">' + names[i] + '</td></tr>');
            }
        } else {
            var selectedNodes = zTree_docTypeTree.getSelectedNodes();
            if (selectedNodes.length > 0) {
                for (var i = 0; i < selectedNodes.length; i++) {
                    zTree_docTypeTree.cancelSelectedNode(selectedNodes[i]);
                }
            }
            $('#vk_doctype_item tbody').html('');
        }
        this.modalId.modal('show');
    }
};

function vk_selectdoctype(hiddenId, typeName, modalId, single) {
    VKDocType.init(hiddenId, typeName, modalId, single);
    VKDocType.treeNode = null;
    VKDocType.show();
}

function vk_del_selectdoctype(hiddenId, typeName) {
    $('#' + hiddenId).val('');
    $('#' + typeName).val('');
}