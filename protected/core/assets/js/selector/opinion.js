


var VKOpinion = {
    urlParams: {
        keyword: ''
    },
    init: function(to_id, to_name, view_id) {
        this.$ti = $('#' + to_id);
        this.$tn = $('#' + to_name);
        this.$vi = $('#' + view_id);
    },
    load: function() {
        $(document).on('click', '#selectopinion .modal-footer a', function() {
            VKOpinion.$vi.modal('hide');
        });
        $(document).on('click', '#vk_opinion_item tbody tr', function() {
            var oldContent = '';
            var tag = VKOpinion.$tn.get(0).tagName.toLowerCase();
            if (tag === 'textarea') {
                oldContent = VKOpinion.$tn.val();
            } else {
                oldContent = VKOpinion.$tn.find('textarea').val();
            }
            var newsign = oldContent + $(this).attr('item_name');
            if (tag === 'textarea') {
                VKOpinion.$tn.val(newsign);
                VKOpinion.$tn.text(newsign);
                VKOpinion.$tn.focus();
            } else {
                VKOpinion.$tn.find('textarea').val(newsign);
                VKOpinion.$tn.find('textarea').text(newsign);
                VKOpinion.$tn.find('textarea').focus();
            }
            VKOpinion.$vi.modal('hide');
        });
        //搜索
        $('#selectopinion .search-query').keyup(function() {
            var keyword = $(this).val();
            VKOpinion.urlParams.keyword = keyword;
            VKOpinion.initItem();
        });
    },
    initItem: function() {
        var parases = VKOpinion.getJsonData();
        var htmlStr = '';
        if (parases) {
            $.each(parases, function(id, content) {
                htmlStr += '<tr item_id="' + id + '" item_name="' + content + '"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>' + content + '</td></tr>';
            });
            $('#vk_opinion_item tbody').html(htmlStr);
        }
    },
    getJsonData: function() {
        var jsonData = '';
        $.ajax({
            type: 'POST',
            url: VKOpinion.baseUrl,
            data: VKOpinion.urlParams,
            dataType: 'json',
            async: false,
            success: function(data) {
                jsonData = data;
            },
            error: function(msg) {
                jsonData = msg;
            }
        });
        return jsonData;
    },
    show: function() {
        this.initItem();
        this.$vi.modal('show');
    }
};


function vk_selectopinion(to_id, to_name, view_id) {
    VKOpinion.init(to_id, to_name, view_id);
    VKOpinion.show();
}