 $('.follow').on('click', function() {
        var a = $(this);
        $.ajax({
            url: a.attr('href'),
            //dataType: 'json',
            success: function(data) {
                if(data == 'create') {
                    a.html('取消关注');
                } else if(data == 'delete'){
                    a.html('点击关注');
                }else{
                    $.notify({type: 'error', message: {text: data, icon: 'icon-close'}}).show();
                }
            },
        });
        return false;
    });
