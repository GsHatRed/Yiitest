


var VKSDept = {

    props : {
        $fieldId : null,
        $fieldName : null,
        $viewId : null,
        baseUrl : null
    },

    load : function() {
        $('#selectsingleorg .modal-footer a').on('click',function(){
            VKSDept.confirm();
        });
        $('#vk_sdept_item tbody tr').live('click', function(){
            $(this).remove();
        });
    },

    init : function(fieldId, fieldName, viewId) {
        this.props['$fieldId'] = $('#'+fieldId);
        this.props['$fieldName'] = $('#'+fieldName);
        this.props['$viewId'] = $('#'+viewId);
    },
    zTreeOnClick : function(treeId,event,treeNode) {
        if($('#dept_'+treeNode.id).length > 0)
            $('#dept_'+treeNode.id).remove();
        else
            $('#vk_sdept_item tbody').html('<tr id="dept_'+treeNode.id+'" item_name="'+treeNode.name+'" item_id="'+treeNode.id+'" class="dept_selected"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="color: #468847;"></span>'+treeNode.name+'</td></tr>');
    },
    confirm : function(){
        var deptName = '',deptId = '';
        if($('#vk_sdept_item tbody tr').length > 0) {
            var selectedtr = $('#vk_sdept_item tbody tr:first');
            deptName = selectedtr.attr('item_name');
            deptId = selectedtr.attr('item_id');
        }
        VKSDept.props.$fieldName.val(deptName);
        VKSDept.props.$fieldId.val(deptId);
        this.props['$viewId'].modal('hide');
    },
    initItem : function(){
        var id = this.props['$fieldId'].val();
        if($('#vk_sdept_item tbody tr').length > 0)
            $('#vk_sdept_item tbody tr').remove();
        if(id && id != ',') {
            var node = zTree_deptsTree.getNodeByParam('id',id,null);
            $('#vk_sdept_item tbody').html('<tr id="dept_'+id+'" item_name="'+node.name+'" item_id="'+id+'" class="dept_selected"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="color: #468847;"></span>'+node.name+'</td></tr>');
        }
    },
    show : function() {
        this.props['$viewId'].modal('show');
        if ($.trim(this.props['$fieldId'].val()) && $.trim(this.props['$fieldName'].val())) {
            VKDept.initItem();
        }
    }
};
function vk_selectsingleorg(to_id, to_name, view_id) {
    VKSDept.init(to_id, to_name, view_id);
    VKSDept.show();
}

function vk_del_selectsingleorg(to_id, to_name) {
    $('#'+to_id).val('');
    $('#'+to_name).val('');
}

