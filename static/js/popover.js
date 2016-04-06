 jQuery(document).ready(function () {
    if($(window).width() < 1230) {
        $(".online").hide();
    }
    if($(window).width() < 1340) {
    	$('.panel-avatar').hide();
    }
    if($(window).height() - 100 < $('.online-scroll').height()) {
        height = $(window).height() - 100;
    } else {
        height = $('.online-scroll').height() + 3;
    }

    $(".online-scroll").height(height);
    $('.online-scroll').perfectScrollbar();
    // back-to-top
    $(window).scroll(function(){
        if ($(this).scrollTop() > 500) {
            $('.back-to-top').fadeIn();
        } else {
            $('.back-to-top').fadeOut();
        }
    });

    $(".back-to-top").click(function(e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "slow");
    });
// tooltip
//data-toggle="tooltip" data-original-title='body'
$("[data-toggle=tooltip]").tooltip({container: 'body'});

 //头像提示会员信息
	$('[rel=author]').popover({
	    trigger : 'manual',
        container: 'body',
	    html : true,
        placement: 'right',
	    content : '<div class="popover-user"></div>',
	}).on('mouseenter', function(){
	    var _this = this;
	    var el = $(this);
	    el.popover('show');
	    $.ajax({
	        url: el.attr('rhref'),
	        success: function(html){
	            $('.popover-user').html(html);
	            $('.popover .btn-success, .popover .btn-danger').click(function(){
	                $.ajax({
	                    url: $(this).attr('href'),
	                    success: function(data) {
	                        $('.popover .btn-success').text('关注成功').addClass('disabled');
	                        $('.popover .btn-danger').text('取消成功').addClass('disabled');
	                    },
	                    error: function (XMLHttpRequest, textStatus) {
	                        $(_this).popover('hide');
	                        $('#modal').modal({ remote: '/login'});
	                        this.abort();
	                    }
	                });
	                return false;
	            });
	        }
	    });
	    $('.popover').on('mouseleave', function () {
	        $(_this).popover('hide');
	    });
	}).on('mouseleave', function () {
	    var _this = this;
	    setTimeout(function () {
	        if(!$('.popover:hover').length) {
	            $(_this).popover('hide')
	        }
	    }, 100);
	});
})

