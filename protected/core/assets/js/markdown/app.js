// namespace
window.application = {
  editor:"",
  apiLimit:1500,
  enabeAutoReload:false,
  enableShortcut:false,
  md:"",
  viewer:"",
  db:localStorage,
  converter:"marked", // default converter is `marked`
  isRendering:false
};
window.URL = window.URL || window.webkitURL;

marked.setOptions({
  gfm: true,
  pedantic: false,
  sanitize: false
});

// Dom Ready
$(function(){
  // button binding
  $(".btn").each(function(){
    var self = this;
        $(self).bind("click",function(event){
      event.preventDefault();
      handleOnClick($(self).attr("id"));
    });
  });
  // Initilize CodeMirror Editor
  application.editor = CodeMirror.fromTextArea(document.getElementById("in"), {
    mode: 'gfm',// github-flavored-markdown
    lineNumbers: true,
    matchBrackets: true,
    theme: "default",
    onFocus:function(){
      $(".CodeMirror-scroll").addClass("focus");
    },
    onBlur:function(){
      $(".CodeMirror-scroll").removeClass("focus");
    },
    onCursorActivity: function() {
      application.editor.setLineClass(hlLine, null, null);
      hlLine = application.editor.setLineClass(application.editor.getCursor().line, null, "activeline");
    }
  });
  var hlLine = application.editor.setLineClass(0, "activeline");
  convert();
})

function handleOnClick(id){
  switch (id) {
    case "btnPrev":
      // exec convert
        convert();
    break;
    case "btnSave":
        save();
        break;
    case "btnCancel":
        window.close();
        break;
    default:
      console.log("Error:invalid case");
    break;
  }
}
//save item
function save(){
    var title = $('#title').val();
    if(!$.trim(title)) {
        return showAlert("标题不能为空！","error")
    }
    if($('#top').attr("checked")){
        var top = $('#top').val();
    }
    application.isRendering = true;
    // save CodeMirror to textarea
    application.editor.save();
    $.ajax({
        'type':'post',
        'url':editor,
        'data':{
            id:$('#help_id').val(),
            main_id:$('#main_id_hidden').val(),
            title:$('#title').val(),
            content:$('#in').val(),
            top:top
        },
        'success':function(data){
            if($('#help_id').val()==''){
                $('#help_id').val(data);
            }
            application.isRendering = false;
            window.opener.save_gird();
            return showAlert("保存成功！");

        },
        'error':function(){}
    });
}

// exec auto reload per 5(sec) if markdown was changed
function autoReload(){
  if (application.enabeAutoReload){
    setTimeout(function(){
      if (application.md != application.editor.getValue()) convert();
      autoReload();
    },5000);
  }
}

// convert markdown to html
function convert(){
    if (application.editor.getValue() != '' && application.md == application.editor.getValue()) return showAlert("内容无变化！","error");
    if (application.isRendering) return showAlert("Now rendering","error");

  application.isRendering = true;
  // save CodeMirror to textarea
  application.editor.save();
  application.md = $("#in").val();
  application.db.setItem("#in",application.md);

  // hide html
  $("#out").fadeOut("fast").empty();

  var convertCallback = function(data,opttionCallback){
    $("#out")
    .addClass("display-none")
    .append(data)
    .fadeIn("fast");
    opttionCallback();
    application.isRendering = false;
    if(application.viewer) application.viewer.location.reload();
  }
      var data = marked(application.md);
      convertCallback(data,function(){
                $('#out pre code').each(function(i, e) {
                    hljs.highlightBlock(e)
      });
            });
  }

// showAlert
function showAlert(msg,option){
    $.notify({
        closable:false,
        type:option,
        message:{
            icon:'icon-info',
            text:msg
}
    }).show();
}

