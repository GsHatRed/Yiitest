var VKWfdf = {
    baseUrl : null,
    block : null,//action
    treeNode:null,
    urlParams:{},

    init : function(hiddenId, wfName, modalId) {
        this.hiddenId = $('#'+hiddenId);
        this.wfName = $('#'+wfName);
        this.modalId = $('#'+modalId);
    },
    load: function() {
        $('#vk_wfdf_tab_category').show();
        VKWfdf.bindClick();
    },
    bindClick: function(){
        //右侧列表点击
        $(document).on('click','#vk_wfdf_item tbody tr',function(){
            if(typeof($(this).attr('item_id')) != 'undefined') {
                $(this).toggleClass('wfdf_selected');
                if($(this).hasClass('wfdf_selected')) {
                    VKWfdf.add($(this).attr('item_id'), $(this).attr('item_name'));
                }
                else {
                    VKWfdf.del($(this).attr('item_id'), $(this).attr('item_name'));
                }
            }
        });
        $('#selectwfdf .modal-footer a').on('click',function(){
            $('#selectwfdf').modal('hide');
        });
    }
    //左侧部门点击事件
    ,
    zTreeOnClick: function(event,treeId,treeNode){
        VKWfdf.treeNode = treeNode;
        VKWfdf.urlParams = {
            action:"sort",
            categoryId:(treeNode.id == '' ? '0' : treeNode.id)
        };
        $('#curCategory').text(treeNode.name);
        VKWfdf.afterClick();
    },
    afterClick: function(){
        var htmlStr = VKWfdf.beforeRenderHtml();
        var $item = $('#vk_wfdf_item');
        if ($item.length > 0) {
            $item.find('tbody').html(htmlStr);
        }
    },
    beforeRenderHtml: function(){
        var htmlStr = '',jsonData='';
        if(VKWfdf.urlParams.hasOwnProperty('categoryId')) {//click
            jsonData = VKWfdf.getJsonData();
            if(!$.isEmptyObject(jsonData)) {
                htmlStr = VKWfdf.getHtmlStr(jsonData);
            }
        }
        return htmlStr;
    },
    getHtmlStr: function(jsonData,trClass) {
        var htmlStr = '';
        var flowName='';
        $.each(jsonData,function(i,formInfo){
            if(formInfo) {
                (formInfo['flow_type']==1)?flowName="固定流程":flowName="自由流程";
                trClass = formInfo['id'] == VKWfdf.hiddenId.val() ? 'wfdf_selected' : '';
                htmlStr += '<tr item_id="'+formInfo['id']+'" item_name="'+formInfo['name']+'" class="'+ trClass +'"><td style="text-align:center;">' +formInfo['name']+'('+flowName+')</td></tr>';
            }
        });
        return htmlStr;
    },
    getJsonData: function(){
        var jsonData = '';
        $.ajax({
            type:'POST',
            url:VKWfdf.baseUrl,
            data:VKWfdf.urlParams,
            dataType:'json',
            async:false,
            success:function(data){
                jsonData = data;
            },
            error: function(msg){
                jsonData = false;
            }
        });
        return jsonData;
    }
    ,
    add: function(item_id, item_name) {
        var $tid = VKWfdf.hiddenId;
        var $tname = VKWfdf.wfName;
        if (!item_id || !item_name)
            return;
        if ($tid.val().indexOf(item_id + ",") !== 0 && $tid.val().indexOf("," + item_id + ",") <= 0) {
            $tid.attr('value', function() {
                return item_id;
            });
            $tname.attr('value',function() {
                return item_name;
            });
        }
        this.modalId.modal('hide');
    }
    ,
    del: function(item_id, item_name) {
        var $tid = VKWfdf.hiddenId;
        var $tname = VKWfdf.wfName;

        if (!item_id || !item_name)
            return;
        $tid.attr('value','');
        $tname.attr('value','');
    },
    show: function() {
        if ((VKWfdf.hiddenId.val() !== "" && VKWfdf.hiddenId.val() != 0) || VKWfdf.wfName.val() !== "")
            $('#vk_wfdf_item tbody').html('<tr item_id="'+VKWfdf.hiddenId.val()+'" item_name="'+VKWfdf.wfName.val()+'" class="wfdf_selected"><td style="text-align:center;">' +VKWfdf.wfName.val()+ '</td></tr>');
        else{
            var selectedNodes = zTree_wfdfTree.getSelectedNodes();
            if(selectedNodes.length > 0) {
                for(var i=0;i<selectedNodes.length;i++)
                    zTree_wfdfTree.cancelSelectedNode(selectedNodes[i]);
            }
            $('#vk_wfdf_item tbody').html('');
        }
        this.modalId.modal('show');
    }
};

function vk_selectwfdf(hiddenId, wfName, modalId) {
    VKWfdf.init(hiddenId, wfName, modalId);
    VKWfdf.treeNode = null;
    VKWfdf.show();
}

function vk_del_selectwfdf(hiddenId, wfName) {
    $('#'+hiddenId).val('');
    $('#'+wfName).val('');
}
