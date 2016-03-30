var AttachmentGrid = {
    pk: '',
    id: '',
    model: '',
    module: '',
    deleteUrl: '',
    init: function(options) {
        this.pk = options.pk;
        this.id = options.id;
        this.model = options.model;
        this.module = options.module;
        this.deleteUrl = options.deleteUrl;
        $("#" + this.id + "-uplaod").live('click', this.showUpload);
        $("#" + this.id + "-selectAll").live('click', this.selectAll);  //全选操作
        $("#" + this.id + "-batchDel").live("click", this.batchDel);   //批量删除
        $("#" + this.id + "-batchDownload").live('click', this.batchDownLoad);//批量下载
        $("#" + this.id + "-attachment-grid tbody input[type=checkbox]").live('click', this.changeIcon);  //改变全选按钮的图标
        $("#" + this.id + "-attachment-grid thead input[type=checkbox]").live('click', this.changeSelectIcon);  //改变全选按钮的图标
        if($("#" + this.model + "-form").length > 0) {
            $("#" + this.model + "-form").append('<input type="hidden" value="' + this.pk + '" id="' + this.id + '_pk" name="pk_id"/><input type="hidden" value="' + this.module + '.models.' + this.model + '" id="' + this.id + '_model" name="model"/>');
            $("#" + this.model + "-form").fileupload({
                dataType: 'json',
                stop: function(e) {
                    var that = $(this).data('fileupload');
                    that._transition($(this).find('.fileupload-progress')).done(
                            function() {
                                $(this).find('.progress')
                                        .attr('aria-valuenow', '0')
                                        .find('.bar').css('width', '0%');
                                $(this).find('.progress-extended').html('&nbsp;');
                                that._trigger('stopped', e);
                            }
                    );
                    $.fn.yiiGridView.update(AttachmentGrid.id + '-attachment-grid');
                }
            });
        }
    },
    showUpload: function() {
        $("#" + AttachmentGrid.id + "_attachment_upload").modal({
            "backdrop": "static",
            "show": true
        });
        AttachmentGrid.changeUpload();
    },
    changeSelectIcon: function() {
        if ($("#" + AttachmentGrid.id + "-selectAll").find("i").hasClass("icon-checkbox-unchecked")) {
            $("#" + AttachmentGrid.id + "-selectAll").find("i").attr("class", "icon-checkbox-checked");
            $("#" + AttachmentGrid.id + "-selectAll").addClass("active");
        } else {
            $("#" + AttachmentGrid.id + "-selectAll").find("i").attr("class", "icon-checkbox-unchecked");
            $("#" + AttachmentGrid.id + "-selectAll").removeClass("active");
        }
    },
    changeUpload: function() {
        if ($("#" + this.id + "_attachment_upload table").find("tr").length > 0) {
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .fileinput-button").addClass("move-right");
            $("#" + this.id + "_attachment_upload .btn-add").addClass("hide-button");
            $("#" + this.id + "_attachment_upload .message-info").hide();
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .start").addClass("move-bottom");
            $("#" + this.id + "_attachment_upload .btn-add").addClass("hide-button");
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .start").show();
        } else {
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .fileinput-button").removeClass("move-right");
            $("#" + this.id + "_attachment_upload .message-info").show();
            $("#" + this.id + "_attachment_upload .btn-add").removeClass("hide-button");
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .start").hide();
            $("#" + this.id + "_attachment_upload .fileupload-buttonbar .start").removeClass("move-bottom");
        }
    },
    selectAll: function() {
        if ($(this).find("i").hasClass("icon-checkbox-unchecked")) {
            $('#' + AttachmentGrid.id + '-attachment-grid thead input[type=checkbox]').attr('checked', 'checked');
            $(this).find("i").attr("class", "icon-checkbox-checked");
            $(this).addClass("active");
            $('#' + AttachmentGrid.id + '-attachment-grid tbody input[type=checkbox]').each(function() {
                $(this).attr('checked', 'checked');
            });
        } else {
            $(this).find("i").attr("class", "icon-checkbox-unchecked");
            $(this).removeClass("active");
            $('#' + AttachmentGrid.id + '-attachment-grid thead input[type=checkbox]').removeAttr('checked');
            $('#' + AttachmentGrid.id + '-attachment-grid tbody input[type=checkbox]').each(function() {
                $(this).removeAttr('checked');
            });
        }
    },
    batchDownLoad: function() {
        var checkedIds = AttachmentGrid.getIds();
        if (checkedIds.length > 0) {
            $('#' + AttachmentGrid.id + '-hideFileId').val(checkedIds);
            $('#' + AttachmentGrid.id + '-formFile').submit();
        } else
            $.notify({
                type: 'warning',
                message: {
                    text: '请至少选中一个文件！',
                    icon: 'icon-error'
                }
            }).show();
    },
    updateGrid: function() {
        $.fn.yiiGridView.update(AttachmentGrid.id + '-attachment-grid');
    },
    batchDel: function() {
        var checkedIds = AttachmentGrid.getIds();
        if (checkedIds.length > 0) {
            window.confirm("确定要批量删除所选的文件吗?", function(ret) {
                if (ret == true) {
                    $.post(AttachmentGrid.deleteUrl, {
                        'ids': checkedIds
                    }, function(data) {
                        if (data == 'OK') {
                            $.notify({
                                type: 'success',
                                message: {
                                    text: '批量删除文件成功！',
                                    icon: 'icon-checkmark'
                                }
                            }).show();
                            $.fn.yiiGridView.update(AttachmentGrid.id + '-attachment-grid');
                        }
                    });
                }
            });
        } else {
            $.notify({
                type: 'warning',
                message: {
                    text: '请至少选中一个文件！',
                    icon: 'icon-error'
                }
            }).show();
        }
    },
    changeIcon: function() {
        var checkedIds = $(this).is(':checked') ? AttachmentGrid.getIds().length : AttachmentGrid.getIds().length - 1;
        var allIds = $("#" + AttachmentGrid.id + "-attachment-grid tbody input[type=checkbox]").length;
        if (checkedIds == allIds) {
            $("#" + AttachmentGrid.id + "-selectAll").find("i").attr("class", "icon-checkbox-checked");
            $("#" + AttachmentGrid.id + "-selectAll").addClass("active");
        } else {
            $("#" + AttachmentGrid.id + "-selectAll").find("i").attr("class", "icon-checkbox-unchecked");
            $("#" + AttachmentGrid.id + "-selectAll").removeClass("active");
        }
    },
    getIds: function() {
        var ids = new Array();
        $('#' + AttachmentGrid.id + '-attachment-grid input[type=checkbox]:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }
}