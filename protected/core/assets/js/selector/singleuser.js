var VKSUser = {
    baseUrl : null,
    block : null,
    treeNode:null,
    urlParams:{},
    aOrgIds:[],
    showOtherOrg:null,
    clickOtherTree:false,
    init : function(hiddenIds, userNames, modalId) {
        this.hiddenIds = $('#'+hiddenIds);
        this.userNames = $('#'+userNames);
        this.modalId = $('#'+modalId);
    },
    load: function() {
        //顶部标签点击事件
        $('#vk_suser_nav_menu a').on('click', function(){
            $(this).parents('li').siblings('li').removeClass('active');
            $(this).parents('li').addClass('active');
            $('#selectsingleuser .tab-pane').hide();//隐藏tab内容区域
            VKSUser.block = $(this).attr('block');//selected 已选 org 部门 role 角色 group 分组  online 在线
            $('#vk_suser_tab_' + VKSUser.block).show();
            VKSUser.urlParams.action = VKSUser.block;
            if(VKSUser.block == 'org') {
                VKSUser.initOrg();
            } else if(VKSUser.block == 'role') {
                VKSUser.initRole();
            } else if(VKSUser.block == 'group'){
                VKSUser.initGroup();
            } else if(VKSUser.block == 'online'){
                VKSUser.initOnline();
            }
            VKSUser.urlParams = {};
        });
        VKSUser.bindClick();
    },
    //触发条件，存在已选用户 0次或1次
    initSelected: function(){
        if(VKSUser.hiddenIds.val() != '' && !$('#vk_suser_selected_item tbody tr').length) {
            var id =  VKSUser.hiddenIds.val();
            var name = VKSUser.userNames.val();
            VKSUser.add(id, name);
        }
    },
    //更新用户选中状态
    initStatus : function(){
        $('#vk_suser_'+VKSUser.block+'_item tbody > tr').removeClass();
        $('#vk_suser_'+VKSUser.block+'_item tbody > tr').find('span').hide();
        if($('#vk_suser_selected_item tbody tr').length > 0){
            var item_id = $('#vk_suser_selected_item tbody tr:first').attr('item_id');
            if($('#vk_suser_'+VKSUser.block+'_item tbody tr[item_id="'+item_id+'"]').length > 0) {
                $('#vk_suser_'+VKSUser.block+'_item tbody tr[item_id="'+item_id+'"]').find('span').show();
                $('#vk_suser_'+VKSUser.block+'_item tbody tr[item_id="'+item_id+'"]').addClass('user_selected');
            }
        }
    },
    initOrg: function(){
        var checkedNodes = zTree_orgSUserTree.getCheckedNodes();
        if(checkedNodes.length > 0) {
            $.each(checkedNodes,function(i,node){
                zTree_orgSUserTree.checkNode(checkedNodes[i],false,true);
            });
        }
        //第一次选人
        if(this.block == 'org' && typeof(VKSUser.urlParams['org']) == 'undefined'){
            VKSUser.urlParams.org = VKSUser.treeNode == null ? VKSUser.curOrgID : VKSUser.treeNode.id;//切换tab时，回来选中上次点击节点
            zTree_orgSUserTree.selectNode(zTree_orgSUserTree.getNodeByParam('id',VKSUser.urlParams.org,null));
            if(VKSUser.treeNode == null) {//初始化 切换tab
                var htmlStr = VKSUser.beforeRenderHtml();
                if(htmlStr != '') {
                    $('#vk_suser_org_item').find('tbody').html(htmlStr);
                }
            }
        }
        VKSUser.initStatus();
    },
    initRole: function(){
        VKSUser.initStatus();
    },
    initGroup: function(){
        VKSUser.initStatus();
    },
    initOnline: function(){
        //每次切换时重新获取在线人员
        VKSUser.urlParams.action = VKSUser.block;
        var onlineUsers = VKSUser.getJsonData();
        var onlineHtml = '';
        $.each(onlineUsers, function(i,userInfo){
            onlineHtml += '<tr item_name="'+userInfo['name']+'" item_id="'+userInfo['id']+'" data-type="user"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>'+userInfo['name']+'</td></tr>';
        });
        $('#vk_suser_'+VKSUser.block+'_item tbody').html(onlineHtml);
        VKSUser.initStatus();
    },
    bindClick: function(){
        //绑定tr click 事件
        $('#selectsingleuser tbody tr[data-type="user"]').live('click',VKSUser.trClickE);

        // 已选人员点击事件 remove
        $('#vk_suser_selected_item tbody > tr').live('click', function() {
            VKSUser.del($(this).attr('item_id'), $(this).attr('item_name'));
        });
        //确认
        $('#selectsingleuser .modal-footer a').on('click',VKSUser.confirm);
        //角色点击事件
        $('#vk_suser_role_menu li').on('click',function(){
            var roleId = $(this).attr('id');
            var roleHtml = '';
            VKSUser.urlParams.role = roleId;
            VKSUser.urlParams.action = VKSUser.block;
            $('#cursRole > i').text($(this).text());
            if(!roleSUserInfo[roleId])
                roleSUserInfo[roleId] = VKSUser.getJsonData();
            if(roleSUserInfo[roleId]) {
                $.each(roleSUserInfo[roleId],function(i,userInfos){
                    if(typeof(userInfos) == 'object' && userInfos !== null){
                        $.each(userInfos,function(userId,userInfo){
                            roleHtml += '<tr item_name="'+userInfo['name']+'" item_id="'+userInfo['id']+'" data-type="user"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>'+userInfo['name']+'</td></tr>';
                        });
                        $('#vk_suser_role_item tbody').html(roleHtml);
                    }
                });
                VKSUser.initStatus();
            }
        });
        //搜索
        $('#selectsingleuser .search-query').keyup(function(){
            var keyword = $(this).val();
            if($.trim(keyword) == '')
                return;
            else {
                $('#vk_suser_nav_menu li').removeClass('active');
                $('#selectsingleuser .tab-pane').hide();
                $('#vk_suser_tab_search').show();
                VKSUser.block = 'search';
                VKSUser.urlParams.action = VKSUser.block;
                VKSUser.urlParams.keyword = keyword;
                var data = VKSUser.getJsonData();
                var htmlStr = '';
                if(data) {
                    $.each(data,function(i,userInfo){
                        if(userInfo) {
                            htmlStr += '<tr item_id="'+userInfo['id']+'" item_name="'+userInfo['user_name']+'" data-type="user"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>' +userInfo['user_name']+ '</td></tr>';
                            VKSUser.updateSelected(userInfo['id'],userInfo['user_name']);
                        }
                    });
                    $('#vk_suser_search_item tbody').html(htmlStr);
                    VKSUser.initStatus();
                } else {
                    $('#vk_suser_search_item tbody').html('<tr><td style="text-align:center;"><strong>没此用户信息！</strong></td></tr>');
                }
            }
        });
    },
    trClickE : function(){
        $('#vk_suser_'+VKSUser.block+'_item tbody > tr').not($(this)).find('span').hide().end().removeClass('user_selected');
        if($(this).hasClass('user_selected')) {
            VKSUser.del($(this).attr('item_id'),$(this).attr('item_name'));
        } else {
            VKSUser.add($(this).attr('item_id'),$(this).attr('item_name'));
        }
        $(this).toggleClass('user_selected');
        $(this).find('span').toggle();
    },
    zTreeOnClick: function(event,treeId,treeNode){
        if(!treeNode.noclick){
            VKSUser.clickOtherTree = treeId === 'orgSOtherUserTree' ? true : false;        
            VKSUser.treeNode = treeNode;
            VKSUser.urlParams = {
                action:"org",
                org:treeNode.id
            };
            $('#vk_suser_org_item #cursOrg i:eq(0)').text(treeNode.name);
            var htmlStr = VKSUser.beforeRenderHtml();
            var $item = $('#vk_suser_'+VKSUser.block+'_item');
            if ($item.length > 0) {
                $item.find('tbody').html(htmlStr);
                VKSUser.initStatus();
            }
        }
    },
    zTreeOnCheck: function(event,treeId,treeNode){
        VKSUser.clickOtherTree = treeId === 'orgSOtherUserTree' ? true : false;
        VKSUser.treeNode = treeNode;
        var aOrgIds = VKSUser.getOrgIds(treeNode,[]);
        if(aOrgIds.length > 1) {
            VKSUser.urlParams = {
                action:"onchecked",
                orgIds:aOrgIds.toString()
            };
            VKSUser.afterCheck();
        }
    },
    afterCheck: function(){
        var $item = $('#vk_suser_org_item');
        var htmlStr = VKSUser.beforeRenderHtml();
        if(htmlStr && $item.length > 0) {
            $item.find('tbody').html(htmlStr);
        }
        VKSUser.initStatus();
    },
    afterUncheck: function(){
        var acheckOrgId = VKSUser.getOrgIds(VKSUser.treeNode, []);
        $.each(acheckOrgId,function(i,orgId){
            if(orgSUserInfo[orgId] && orgSUserInfo[orgId] != 'undefined') {
                $.each(orgSUserInfo[orgId],function(i,userInfo){
                    if(userInfo) {
                        VKSUser.del(userInfo['id'], userInfo['user_name']);
                    }
                });
            }
        });
        VKSUser.initStatus();
    },
    beforeRenderHtml: function(){
        var htmlStr = '',jsonData='';
        if(VKSUser.urlParams.hasOwnProperty('org')) {//click
            htmlStr = VKSUser.getHtmlStr(VKSUser.urlParams.org);
            if(htmlStr == '') {
                jsonData = VKSUser.getJsonData();
                if(!$.isEmptyObject(jsonData)) {
                    orgSUserInfo[VKSUser.urlParams.org] = jsonData[VKSUser.urlParams.org];
                    htmlStr = VKSUser.getHtmlStr(VKSUser.urlParams.org);
                }
            }
        } else if(typeof(VKSUser.urlParams['orgIds']) != 'undefined') {//checked
            var aorgIds = VKSUser.urlParams.orgIds.split(',');
            var newIds = '';
            //过滤获取不在orgUserInfo数组中的部门 newIds
            $.each(aorgIds,function(i,orgId){
                if(orgSUserInfo[orgId] && typeof(orgSUserInfo[orgId]) != 'undefined')
                    return true;
                else
                    newIds += orgId+',';
            });
            newIds = newIds.substr(0,newIds.length - 1);
            if(newIds != '') {
                VKSUser.urlParams.orgIds = newIds;
                jsonData = VKSUser.getJsonData();
                $.each(newIds.split(','),function(i,orgId){
                    if(typeof(jsonData[orgId]) != 'undefined')
                        orgSUserInfo[orgId] = jsonData[orgId];//放入数组中
                });
            }
            $.each(aorgIds, function(i,orgId){
                htmlStr += VKSUser.getHtmlStr(orgId);
            });
        }
        return htmlStr;
    },
    getHtmlStr: function(orgId,trClass) {
        trClass = typeof(trClass) == 'undefined' ? "" : trClass;
        var htmlStr = '';
        if(typeof(orgSUserInfo[orgId]) != 'undefined') {
            if(VKSUser.clickOtherTree) {
                htmlStr += '<tr style="background-color:#c4de83;"><td style="text-align:center;">'+zTree_orgSOtherUserTree.getNodeByParam('id',orgId,null).name+'</td></tr>';
            } else {
                htmlStr += '<tr style="background-color:#c4de83;"><td style="text-align:center;">'+zTree_orgSUserTree.getNodeByParam('id',orgId,null).name+'</td></tr>';
            }
            $.each(orgSUserInfo[orgId],function(i,userInfo){
                if(userInfo)
                    htmlStr += '<tr item_id="'+userInfo['id']+'" item_name="'+userInfo['user_name']+'" class="'+ trClass +'" data-type="user"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>' +userInfo['user_name']+ '</td></tr>';
            });
        }
        return htmlStr;
    },
    getJsonData: function(){
        var jsonData = '';
        $.ajax({
            type:'POST',
            url:VKSUser.baseUrl,
            data:VKSUser.urlParams,
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
    } ,
    getOrgIds: function(treeNode,aOrgIds) {
        if(!treeNode.noclick) {
            aOrgIds.push(treeNode.id);
        }
        if(treeNode.hasOwnProperty('children')) {
            var len = treeNode.children.length;
            for(var i=0; i< len; i++){
                VKSUser.getOrgIds(treeNode.children[i],aOrgIds);
            }
        }
        return aOrgIds;
    }
    ,
    add: function(item_id, item_name) {
        VKSUser.updateSelected(item_id, item_name, 'add');
    },
    del: function(item_id, item_name) {
        VKSUser.updateSelected(item_id, item_name, 'remove');
    },
    updateSelected: function(item_id,item_name,f) {
        var b = $('#vk_suser_selected_item tbody tr[item_id="'+item_id+'"]').length > 0;
        if(f == 'add' && !b) {
            $('#vk_suser_selected_item tbody').html('<tr item_name="'+item_name+'" item_id="'+item_id+'" class="user_selected"><td style="text-align:center;">'+item_name+'</td></tr>');
        } else if(f == 'remove' && b) {
            $('#vk_suser_selected_item tbody tr[item_id="'+item_id+'"]').remove();
        }
    },
    confirm: function(){
        var backfillIds = '',backfillNames='';
        var selectTr = $('#vk_suser_selected_item tbody > tr:first');
        if(selectTr.length > 0) {
            backfillIds = selectTr.attr('item_id');
            backfillNames = selectTr.attr('item_name');
            VKSUser.hiddenIds.val(backfillIds);
            VKSUser.userNames.val(backfillNames);
        } else {
            VKSUser.hiddenIds.val(backfillIds);
            VKSUser.userNames.val(backfillNames);
        }
        $('#selectsingleuser').modal('hide');
    },
    show: function() {
        $('#vk_suser_selected_item tbody').html('');
        $('#selectsingleuser #selected-counter').html('');
        $('#selectsingleuser .search-query').val('');
        if ($.trim(this.hiddenIds.val()) && $.trim(this.userNames.val())) {
            $('#vk_suser_nav_menu a[block="selected"]').trigger('click');
            VKSUser.initSelected();
        } else {
            $('#vk_suser_nav_menu a[block="org"]').trigger('click');
        }
        this.modalId.modal('show');
    }
};

function vk_selectsingleuser(hiddenIds, userNames, modalId) {
    VKSUser.init(hiddenIds, userNames, modalId);
    VKSUser.treeNode = null;
    VKSUser.show();
}

function vk_del_selectsingleuser(hiddenIds, userNames) {
    $('#'+hiddenIds).val('');
    $('#'+userNames).val('');
}