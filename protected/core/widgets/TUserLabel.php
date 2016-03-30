<?php

/**
 * TUserLabel class file.
 *
 */
Yii::import('core.widgets.TWidget');

/**
 * 用户标签类
 */
class TUserLabel extends TWidget {

    public $scriptFile = '//jquery/jquery.popbox.js';
    public $cssFile = array('//plugins/jquery.popbox.css', 'userinfo.css');
    public $users = array();
    public $direction = 'left';
    public $htmlOptions = array();

    public function init() {
        if (is_string($this->users))
            $this->users = explode(',', $this->users);
        $this->htmlOptions['class'] = 'user-label ' . $this->htmlOptions['class'];
        $this->htmlOptions['target'] = '_blank';
        parent::init();
    }

    public function run() {
        $this->renderLabels();

        $userLabelJs = '
         $(".user-label").live("mouseover",function(event){
            $(this).popBox({
                direction: "'.$this->direction.'",
                boxType: "user-label-box",
                holder: $(".user-label"),
                ajax: {
                     type: "POST",
                     url: "' . Yii::app()->createUrl('/portal/default/userinfo') . '",
                     dataType: "json",
                     data: function(el){
                        return {id: el.attr("data-id")}
                     }
                },
                contentBoxStyle: {width: "300px"},
                dataFormat: function(d){
                    var $tpl = $(\'<div class="userinfo_wrap" node-type="bd_data"><div class="bd"><div class="bd_pic" node-type="bd_pic"></div><div class="bd_txt" node-type="bd_txt"></div></div><div class="ft" style="overflow:visible"><div class="ft_funcs"><a class="funcs_item" title="向此联系人发送微讯" href="javascript:;"><span class="nllink icon-bubble-10" node-type="chat"></span></a><a class="funcs_item" title="向此联系人发送电子邮件" href="javascript:;"><span class="nllink icon-envelop" node-type="sendEmail"></span></a><div class="btn-group ft-more"><a data-toggle="dropdown" class="btn btn-mini funcs_item more-opt dropdown-toggle" title="更多选项" href="javascript:;"><span class="caret"></span></a><ul  class="dropdown-menu"><li><a node-type="sendSms" tabindex="-1" href="javascript:;">发送手机短信</a></li></ul></div></div><div class="ft_txt"><a href="javascript:;" node-type="detailInfo" title="查看此联系人的详细信息"><span class="label">详细资料</span></a> <a href="javascript:;" node-type="exEmail" title="查看近期与此联系人往来的电子邮件"><span class="label">来往邮件</span></a></div></div></div>\');
                    $tpl.attr("node-data-id", d.user_id);
                    $tpl.attr("node-data-name", d.user_name);
                    $tpl.attr("node-data-avatar", d.avatar);
                    var tpl_pic = $tpl.find("[node-type=\'bd_pic\']");
                    var tpl_txt = $tpl.find("[node-type=\'bd_txt\']");
                    if (d)
                    {
                        if (d.avatar) {
                            tpl_pic.append($("<img></img>").attr("src", d.avatar));
                        } else {
                            //todo 根据性别来增加不同的默认头像
                        }

                        if (d.user_name)
                            tpl_txt.append(\'<span class="bd_stongdesc T_fs14">\' + d.user_name + \'</span>\');
                        if (d.dept_name)
                            tpl_txt.append(\'<span class="bd_desc">部门：\' + d.dept_name + \'</span>\');
                        if (d.role_name)
                            tpl_txt.append(\'<span class="bd_desc">角色：\' + d.role_name + \'</span>\');
                        if (d.mobile)
                            tpl_txt.append(\'<span class="bd_desc">手机：\' + d.mobile + \'</span>\');
                        if (d.tel_dept)
                            tpl_txt.append(\'<span class="bd_desc">工作电话：\' + d.tel_dept + \'</span>\');

                        return $tpl;                
                    }
                }
            });
            event.preventDefault();
         });
          var exEmailUrl = "' . Yii::app()->controller->createUrl("/email/inbox/exemail", array("id" => "pk")) . '";  //查看来往邮件
          var sendEmailUrl= "' . Yii::app()->controller->createUrl("/email/create/index", array("action"=>"send","user_id" => "pk")) . '";   //发送邮件
          var userDetailUrl = "' . Yii::app()->controller->createUrl("/portal/default/userdetail", array("id" => "pk")) . '";  //查看用户详细信息
          var sendSmsUrl = "' . Yii::app()->controller->createUrl("/sms/default/create", array("user_id" => "pk")) . '";  //发送手机短信
          var sendMessageUrl = "' . Yii::app()->controller->createUrl("/message/message/create", array("user_id" => "pk")) . '";  //发送微讯
            var _left=(screen.availWidth-780)/2;
            var _top=100;
            var _width=780;
            var _height=500;
       $(".userinfo_wrap").find("a[node-type=\'exEmail\']").live("click",function(){
            var  userId =  $(this).parents(".userinfo_wrap").attr("node-data-id");
            TUtil.openUrl(exEmailUrl.replace(\'pk\',userId),"", "", 780, 500);
        });
        $(".userinfo_wrap").find("span[node-type=\'sendEmail\']").live("click",function(){
             var  userId =  $(this).parents(".userinfo_wrap").attr("node-data-id");
            TUtil.openUrl(sendEmailUrl.replace(\'pk\',userId));
        });
        $(".userinfo_wrap").find("a[node-type=\'detailInfo\']").live("click",function(){
            var  userId =  $(this).parents(".userinfo_wrap").attr("node-data-id");
            TUtil.openUrl(userDetailUrl.replace(\'pk\',userId),"", "user_detail", 850, 500);
        });
        $(".userinfo_wrap").find("span[node-type=\'chat\']").live("click",function(){
            var  userId =  $(this).parents(".userinfo_wrap").attr("node-data-id");
            TUtil.openUrl(sendMessageUrl.replace(\'pk\',userId),"", "发送微讯", 850, 500);
        });
        $(".userinfo_wrap").find("a[node-type=\'sendSms\']").live("click",function(){
            var  userId =  $(this).parents(".userinfo_wrap").attr("node-data-id");
            TUtil.openUrl(sendSmsUrl.replace(\'pk\',userId),"", "", 850, 500);
        });
        ';
        Yii::app()->clientScript->registerScript('userLableJs', $userLabelJs);
    }

    public function renderLabels() {
        foreach ($this->users as $userId) {
            echo CHtml::openTag('div', array('class' => 'user-label-container'));
            $this->htmlOptions['data-id'] = $userId;
            echo CHtml::link(User::getNameById($userId), Yii::app()->controller->createUrl('/portal/default/userdetail',array('id'=>$userId)), $this->htmlOptions);
            echo CHtml::closeTag('div');
        }
    }

}
?>
