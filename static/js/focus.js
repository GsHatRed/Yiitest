 $('.follow').on('click', function() {
        var a = $(this);
        $.ajax({
            url: a.attr('href'),
            //dataType: 'json',
            success: function(data) {
                if(data == 'create') {
                    a.html('取消关注');
                } else {
                    a.html('点击关注');
                }
            },
        });
        return false;
    });
