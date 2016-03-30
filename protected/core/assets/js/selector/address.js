var VKAddress = {
    ajaxUrl : null,
    block : null,//action
    treeNode:null,
    urlParams:{},

    init : function(hiddenId, formName, modalId) {
        this.hiddenId = $('#'+hiddenId);
        this.formName = $('#'+formName);
        this.modalId = $('#'+modalId);
    },
    load: function() {
        VKAddress.bindClick();
        $('#vk_address_tab_sort').show();
    },
    bindClick: function(){
        //右侧列表点击
        $(document).on('click','#vk_address_item tbody tr',function(){
            $(this).toggleClass('address_selected');
            $(this).find('span').toggle();
            var itemName=$(this).find("td").text();
            var itemId=$(this).attr('id');
            if($('#selectaddress .modal-footer #item_'+itemId).length>0){
                $('#selectaddress .modal-footer #item_'+itemId).remove();
                VKAddress.updateSelector(itemId,itemName,'del');
            }else{
                $('#selectaddress .modal-footer a').before('<span id="item_'+itemId+'" class="label label-success" style="float:left;margin-left:3px;">'+itemName+'</span>');
                VKAddress.updateSelector(itemId,itemName,'add');
            }
        });
        $('#selectaddress .modal-footer a').on('click',function(){
            $('#selectaddress').modal('hide');
        });
        $('#selectaddress i[name="checkStatu"]').live('click',function(){
            var curClass = $(this).attr('class');
            if(curClass == 'icon-checkbox-unchecked') {
                if($('#vk_address_item tbody tr').length > 0) {
                    $('#vk_address_item tbody tr').each(function(){
                        $(this).addClass('address_selected');
                        $(this).find('span').show();
                        var itemName=$(this).find("td").text();
                        var itemId=$(this).attr('id');
                        if($('#selectaddress .modal-footer #item_'+itemId).length == 0){
                            $('#selectaddress .modal-footer a').before('<span id="item_'+itemId+'" class="label label-success" style="float:left;margin-left:3px;">'+itemName+'</span>');
                            VKAddress.updateSelector(itemId,itemName,'add');
    }
                    });
                }
                $(this).removeClass(curClass).addClass('icon-checkbox-checked');
                $(this).attr('data-original-title','全部取消');
            } else {
                if($('#vk_address_item tbody tr[class="address_selected"]').length > 0) {
                    $('#vk_address_item tbody tr[class="address_selected"]').each(function(){
                        var itemName=$(this).find("td").text();
                        var itemId=$(this).attr('id');
                        $(this).removeClass('address_selected');
                        $(this).find("span").hide();
                        $('#selectaddress .modal-footer #item_'+itemId).remove();
                        VKAddress.updateSelector(itemId,itemName,'del');
                    });
                }
                $(this).removeClass(curClass).addClass('icon-checkbox-unchecked');
                $(this).attr('data-original-title','全选');
            }
        });
    }
    //左侧部门点击事件
    ,
    zTreeOnClick: function(event,treeId,treeNode){
        $('#selectaddress i[name="checkStatu"]').removeClass('icon-checkbox-checked').addClass('icon-checkbox-unchecked');
        $.post(VKAddress.ajaxUrl,{
            groupId:treeNode.id
        },function(data){
            var data=$.parseJSON(data);
            $('#vk_address_item tbody').html('');
            var html='';
            $.each(data,function(key,value){
                html+=VKAddress.tableHtml(value.id,value.name);
            })
            $('#vk_address_item tbody').append(html);
        });
    },
    tableHtml:function(id,name){
        if($('#selectaddress .modal-footer #item_address_'+id).length>0){
            return '<tr id="address_'+id+'" class="address_selected"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="color: #468847;"></span>'+name+'</td></tr>'
        }else
            return '<tr id="address_'+id+'"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>'+name+'</td></tr>'
    },

    updateSelector: function(id, name ,operator) {
        var idStr='',nameStr='';
        if(operator=='add'){
            idStr=$(VKAddress.hiddenId).val();
            nameStr=$(VKAddress.formName).val();
            idStr+=id.substr('8')+',';
            nameStr+=name+',';
            $(VKAddress.hiddenId).val(idStr);
            $(VKAddress.formName).val(nameStr);
        }else{
            idStr=$(VKAddress.hiddenId).val();
            nameStr=$(VKAddress.formName).val();
            $(VKAddress.hiddenId).val(idStr.replace(id.substr('8')+',',""));
            $(VKAddress.formName).val(nameStr.replace(name+',',""));
        }
    },

    show: function() {
        if ((this.hiddenId.val() !== "" && this.hiddenId.val() != 0) || this.formName.val() !== "") {
            $('#vk_address_item tbody').html('<tr item_id="'+this.hiddenId.val()+'" item_name="'+this.formName.val()+'" class="form_selected"><td style="text-align:center;">' +this.formName.val()+ '</td></tr>');
        }
        this.modalId.modal('show');
    }
};

function vk_selectaddress(hiddenId, formName, modalId) {
    VKAddress.init(hiddenId, formName, modalId);
    VKAddress.treeNode = null;
    VKAddress.show();
    //获取选中的节点
    var treeObj = $.fn.zTree.getZTreeObj("addressTree");
    var nodes = treeObj.getSelectedNodes();
    if(nodes[0]==null || nodes[0]=='undefined'){
        return false;
    }else{
        $.post(VKAddress.ajaxUrl,{
            groupId:nodes[0].id
        },function(data){
                var data=$.parseJSON(data);
                $('#vk_address_item tbody').html('');
                var html='';
                $.each(data,function(key,value){
                    html+=VKAddress.tableHtml(value.id,value.name);
                })
                $('#vk_address_item tbody').append(html);
        });
    }
}

function vk_del_selectaddress(hiddenId, formName) {
    $('#'+hiddenId).val('');
    $('#'+formName).val('');
    $('#selectaddress .modal-footer').find('span').remove();
    //清除选中节点
    var treeObj = $.fn.zTree.getZTreeObj("addressTree");
    treeObj.cancelSelectedNode();

    $('#vk_address_item tbody').html('');
}