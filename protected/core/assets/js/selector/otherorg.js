


var VKOrg = {

    props : {
        $fieldId : null,
        $fieldName : null,
        $viewId : null,
        baseUrl : null
    },

    load : function() {
        $(document).on('click', '#vk_org_tree a[key]', function(){
            VKOrg.get($(this).attr('key'));
        });
        $('#selectorg .modal-footer a').on('click',function(){
            VKOrg.confirm();
        });
        $('#vk_org_item li').live('click', function(){
            $(this).remove();
        });
    },

    init : function(fieldId, fieldName, viewId) {
        this.props['$fieldId'] = $('#'+fieldId);
        this.props['$fieldName'] = $('#'+fieldName);
        this.props['$viewId'] = $('#'+viewId);
        VKOrg.initItem();
    },
    zTreeOnClick : function(treeId,event,treeNode) {
        if($('#org_'+treeNode.id).length > 0)
            $('#org_'+treeNode.id).remove();
        else
            $('#vk_org_item ul').append('<li id="org_'+treeNode.id+'" item_name="'+treeNode.name+'" item_id="'+treeNode.id+'" class="active"><a>'+treeNode.name+'</a></li>');
    },
    confirm : function(){
        var deptNames = '',deptIds='';
        if($('#vk_org_item ul li').length > 0) {
            $('#vk_org_item ul li').each(function(){
                deptNames += $(this).attr('item_name') + ',';
                deptIds += $(this).attr('item_id') + ',';
            });
        }
        VKOrg.props.$fieldName.val(deptNames);
        VKOrg.props.$fieldId.val(deptIds);
        this.props['$viewId'].modal('hide');
    },
    initItem : function(){
        var ids = this.props['$fieldId'].val();
        if($('#vk_org_item ul li').length > 0)
            $('#vk_org_item ul li').remove();
        if(ids && ids != ',') {
            ids = ids.substr(ids.length-1) == ',' ? ids.substr(0,ids.length-1) : ids;
            var idArr = ids.split(',');
            var names = '';
            $.each(idArr, function(i,id){
                //@test
                var node = zTree_deptTree.getNodeByParam('id',id,null);
                names += node.name+',';

                if($('#org_'+id).length > 0)
                    return true;
               $('#vk_org_item ul').append('<li id="org_'+id+'" item_name="'+node.name+'" item_id="'+id+'" class="active"><a>'+node.name+'</a></li>');
            });
        }
    },
    get : function(key) {
        $.getJSON(this.props['baseUrl'] + '&key=' + key, function(data){
            $('#vk_org_item').html(data.html);
            VKOrg.initItem();
        });
    },

    initItem_back : function() {
        var ids = this.props['$fieldId'].val();
        $('#vk_org_item a').each(function(){
            var id = $(this).attr('item_id');
            if(ids.indexOf(id + ',') === 0 || ids.indexOf(','+id+',') > 0 || ids === id) {
                $(this).parents('li').addClass('active');
            } else {
                $(this).parents('li').removeClass('active');
            }
        });
    },

    add : function(Obj) {
        var tid = this.props['$fieldId'];
        var tname = this.props['$fieldName'];
        var id = Obj.attr('item_id');
        var name = Obj.attr('item_name');

        if(tid.val().indexOf(id+',') !== 0 && tid.val().indexOf(','+id+',') <= 0) {
            tid.attr('value', function() {
                return this.value + id + ',';
            });
            tname.attr('value', function() {
                return this.value + name + ',';
            });
        }
    },

    del : function(Obj) {
        var tid = this.props['$fieldId'];
        var tname = this.props['$fieldName'];
        var id = Obj.attr('item_id');
        var name = Obj.attr('item_name');

        if(tid.val().indexOf(id+',') === 0 || tid.val().indexOf(','+id+',') > 0) {
            if(tid.val().indexOf(id+',')===0)
                tid.val(tid.val().substr(id.length + 1));
            else
                tid.val(tid.val().replace(','+id+',', ','));

            if(tname.val().indexOf(name+',') === 0)
                tname.val(tname.val().substr(name.length + 1));
            else
                tname.val(tname.val().replace(','+name+',', ','));
        }
    },

    show : function() {
        this.props['$viewId'].modal('show');
    }
};

function vk_selectorg(to_id, to_name, view_id) {
    VKOrg.init(to_id, to_name, view_id);
    VKOrg.show();
}

function vk_del_selectorg(to_id, to_name) {
    $('#'+to_id).val('');
    $('#'+to_name).val('');
}

