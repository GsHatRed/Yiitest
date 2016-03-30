var VKForm = {
    baseUrl : null,
    block : null,//action
    treeNode:null,
    single:false,
    urlParams:{},

    init : function(hiddenId, formName, modalId, single) {
        this.hiddenId = $('#'+hiddenId);
        this.formName = $('#'+formName);
        this.modalId = $('#'+modalId);
        this.single = (single == 1 ? true : false);
    },
    load: function() {
        VKForm.bindClick();
        $('#vk_form_tab_sort').show();
    },
    bindClick: function(){
        //右侧列表点击
        $(document).on('click','#vk_form_item tbody tr',function(){
            if(typeof($(this).attr('item_id')) != 'undefined') {
                $(this).toggleClass('form_selected');
                if($(this).hasClass('form_selected')) {
                    VKForm.add($(this).attr('item_id'), $(this).attr('item_name'));
                }
                else {
                    VKForm.del($(this).attr('item_id'), $(this).attr('item_name'));
                }
            }
        });
        $('#selectform .modal-footer a').on('click',function(){
            $('#selectform').modal('hide');
        });
    }
    //左侧部门点击事件
    ,
    zTreeOnClick: function(event,treeId,treeNode){
        VKForm.treeNode = treeNode;
        VKForm.urlParams = {
            action:"sort",
            sortId:(treeNode.id == '' ? '0' : treeNode.id)
        };
        $('#curSort').text(treeNode.name);
        VKForm.afterClick();
    },
    afterClick: function(){
        var htmlStr = VKForm.beforeRenderHtml();
        var $item = $('#vk_form_item');
        if ($item.length > 0) {
            $item.find('tbody').html(htmlStr);
        }
    },
    beforeRenderHtml: function(){
        var htmlStr = '',jsonData='';
        if(VKForm.urlParams.hasOwnProperty('sortId')) {//click
            jsonData = VKForm.getJsonData();
            if(!$.isEmptyObject(jsonData)) {
                htmlStr = VKForm.getHtmlStr(jsonData);
            }
        }
        return htmlStr;
    },
    getHtmlStr: function(jsonData,trClass) {
        var htmlStr = '';
        $.each(jsonData,function(i,formInfo){
            if(formInfo) {
                trClass = formInfo['id'] == VKForm.hiddenId.val() ? 'form_selected' : '';
                htmlStr += '<tr item_id="'+formInfo['id']+'" item_name="'+formInfo['form_name']+'" class="'+ trClass +'"><td style="text-align:center;">' +formInfo['form_name']+ '</td></tr>';
            }
        });
        return htmlStr;
    },
    getJsonData: function(){
        var jsonData = '';
        $.ajax({
            type:'POST',
            url:VKForm.baseUrl,
            data:VKForm.urlParams,
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
    },
    counter : function(n){
        if(n === 0){
            $('#form_counter')&&$('#form_counter').parent().remove();
            return;
        }
        if($('#form_counter').length > 0){
            var num = parseInt($('#form_counter').text());
            $('#form_counter').text(num+n);
            if(num+n == 0)
                $('#form_counter').parent().remove();
        } else if(n > 0){
            this.modalId.find('.modal-footer a').before('<span class="pull-left">共选中 <em id="form_counter" style="font-size:20px;color:#f21717">1</em> 个表单</span>');
        }
    },
    add: function(item_id, item_name) {
        var self = this;
        var $tid = this.hiddenId;
        var $tname = this.formName;
        if (!item_id || !item_name)
            return;
        if ($tid.val().indexOf(item_id + ",") !== 0 && $tid.val().indexOf("," + item_id + ",") <= 0) {
            $tid.attr('value', function() {
                if(self.single === true)
                    return item_id;
                else
                    return this.value + item_id + ',';
            });
            $tname.attr('value',function() {
                if(self.single === true)
                    return item_name;
                else 
                    return this.value + item_name + ',';
            });
            if(self.single === false){
                this.counter(1);
            }
        }
        if(self.single === true)
            this.modalId.modal('hide');
    }

    ,
    del: function(item_id, item_name) {
        var $tid = this.hiddenId;
        var $tname = this.formName;

        if (!item_id || !item_name)
            return;
        
        if ($tid.val().indexOf(item_id + ",") === 0 || $tid.val().indexOf("," + item_id + ",") > 0) {
            $tid.attr('value', function() {
                if(self.single === true)
                    return '';
                else 
                    return this.value.replace(item_id + ',', '');
            });
            $tname.attr('value',function() {
                if(self.single === true)
                    return '';
                else 
                    return this.value.replace(item_name + ',', '');
            });
            if(this.single === false){
                this.counter(-1);
            }
        }
    },
    show: function() {
        if ((this.hiddenId.val() !== "" && this.hiddenId.val() != 0) || this.formName.val() !== "") {
            $('#vk_form_item tbody').empty();
            this.counter(0);
            var ids = this.hiddenId.val().split(',');
            var names = this.formName.val().split(',');
            for(i in ids){
                if(ids[i] == ',' || ids[i] == '')
                    continue;
                $('#vk_form_item tbody').append('<tr item_id="'+ids[i]+'" item_name="'+names[i]+'" class="form_selected"><td style="text-align:center;">' +names[i]+ '</td></tr>');
                if(this.single === false)
                    this.counter(1);
            }
        } else {
            var selectedNodes = zTree_formTree.getSelectedNodes();
            if(selectedNodes.length > 0) {
                for(var i=0;i<selectedNodes.length;i++)
                    zTree_formTree.cancelSelectedNode(selectedNodes[i]);
            }
            this.counter(0);
            $('#vk_form_item tbody').html('');
        }
        this.modalId.modal('show');
    }
};

function vk_selectform(hiddenId, formName, modalId, single) {
    VKForm.init(hiddenId, formName, modalId, single);
    VKForm.treeNode = null;
    VKForm.show();
}

function vk_del_selectform(hiddenId, formName) {
    $('#'+hiddenId).val('');
    $('#'+formName).val('');
}