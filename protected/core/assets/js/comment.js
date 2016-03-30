    $(document).ready(function(){    
        $("#submitComment").live("click",function(){
            trimRegex = /(?:^[ \t\n\r]+)|(?:[ \t\n\r]+$)/g;
            var content = CKEDITOR.instances.SysComment_content.getData();
            if(content==""){
                alert("请输入会签意见");
            }else{
                var action = $("#comment-form").attr("action") ;
               
                $.ajax({
                    url:action,
                    data:{content:content,model:$("#SysComment_model").val(),pk:$("#SysComment_pk").val()},
                    type:"POST",
                    success:function(result){
                        if(result!="ERROR"){                                  
                            $(".comment_list:first").prepend(result);
                            window.confirm("发表成功,点击确定查看",function(ret){
                                if(ret==true){
                                    window.location.reload();
                                }
                            })
                        }else{
                            alert("发送失败");
                        }
                    }
                });
            };
        });
    });
    $(".reply").live("click",function(){
        $(".replyContent").hide();
        var id=$(this).attr("id");
        $("#comment"+id).val(id);
        $("#reply"+id).show(500);
        $("#replyContent"+id).focus();
    });
    $(document).delegate(".replyList", 'mouseenter', function() {
        $(this).find(".reply").show();
    });
   $(document).delegate(".replyList", 'mouseleave', function() {
       $(this).find(".reply").hide();
   });
    
    var Comment = {
        sendReply:function(id){
            var parent_id = $("#comment"+id).val();
            var content = $("#replyContent"+id).val();
            if(content==""){
                alert("回复内容不能为空");
                return false;
            }
            var model = $("#modelName").val();
            var pk = $("#pk").val();
            $.ajax({
                url:$("#comment-form").attr("action"),
                data:{parent_id:parent_id,content:content,model:model,pk:pk},
                type:"POST",
                success:function(result){
//                    $(".replyContent").hide();
//                    document.getElementById("replyContent"+id).value="";
//                    $("#"+id).parents(".R_msg").append(result);
                       window.location.reload();
                }
            });
        }
    }