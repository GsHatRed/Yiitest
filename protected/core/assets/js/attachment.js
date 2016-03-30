var Attachment = {
    init: function(options) {
        $.extend(this.callback, options || {});
    },
    callback: {
        afterDelete: ''
    },
    deleteAttach: function(deleteURL) {
        window.confirm('确认要删除该附件？', function(ret) {
            if (ret == true) {
                $.ajax({
                    type: 'post',
                    url: deleteURL,
                    dataType: 'json',
                    success: function(data) {
                        if (data.flag === 'OK') {
                            $("#attachment_" + data.fileId).remove();
                            if (typeof Attachment.callback.afterDelete === 'function') {
                                Attachment.callback.afterDelete(data.id);
                            }
                            $.notify({type: 'success', message: {text: '附件删除成功！', icon: 'icon-checkmark'}}).show();
                        } else {
                            $.notify({type: 'error', message: {text: '附件删除失败！', icon: 'icon-checkmark'}}).show();
                        }
                    }
                });
            }
        })
    },
    rename: function(renameURL, fileId) {
        $('#attachmentRename').modal({
            "backdrop": 'static',
            "show": true
        });
        var fileName = $("#attachment_" + fileId).find("a.dropdown-toggle").text();
        $('#attachmentRename .file_id').val(fileId);
        $('#attachmentRename .rename_url').val(renameURL);
        $("#attachmentRename .file_name").val(fileName.substring(0, fileName.lastIndexOf('.')));
        var fileExt = fileName.substring(fileName.lastIndexOf('.'), fileName.length);
        $("#attachmentRename .file_name").next().text(fileExt);
    },
    onRenameSubmit: function() {
        var fileId =  $('#attachmentRename').find('.file_id').val();
        var renameURL =  $('#attachmentRename').find('.rename_url').val();
        var fileName = $("#attachmentRename").find(".file_name").val();
        $.ajax({
            type: 'post',
            url: renameURL,
            data: {"fileName": fileName},
            dataType: "json",
            success: function(data) {
                if (data.message == "") {
                    $("#attachmentRename .modal-header").find("div").html("附件名称不能为空").show();
                    setTimeout(function() {
                        $("#attachmentRename .modal-header").find("div").hide()
                    }, 2000);
                } else if (data.message == "error") {
                    $("#attachmentRename .modal-header").find("div").html("附件重命名失败").show();
                    setTimeout(function() {
                        $("#attachmentRename .modal-header").find("div").hide()
                    }, 2000);
                } else {
                    $("#attachmentRename").modal("hide");
                    var attachObj = $("#attachment_" + fileId);
                    var src = attachObj.find("a.dropdown-toggle img").attr("src");
                    var newUrl = attachObj.find("a.dropdown-toggle").attr("href").replace(data.oldFileName, data.newFileName);
                    newUrl = attachObj.find("a.dropdown-toggle").attr("href", newUrl);
                    attachObj.find("a.dropdown-toggle").html("<img src=" + src + ">" + data.fileName)
                    $("#attachment_" + fileId + " ul li").each(function() {
                        var newcUrl = $(this).children().attr("href").replace(data.oldFileName, data.newFileName);
                        $(this).children().attr("href", newcUrl);

                    });
                    $.notify({type: "success", message: {text: "附件重命名成功！", icon: "icon-checkmark"}}).show();
                }
            }
        });
    },
    saveAs: function() {
        console.log('保存到个人文件柜');
    },
    insertImage: function(editorId, editorType, src) {
        if (editorType === 'CKEditor' && editorId !== '') {
            TCKEditorHelper.insertImage(editorId, src);
        }
    }
};