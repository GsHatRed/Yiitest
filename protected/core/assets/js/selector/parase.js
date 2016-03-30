


var VKParase = {
    urlParams: {
        keyword: ''
    },
    init: function(to_id, to_name, view_id) {
        this.$ti = $('#' + to_id);
        this.$tn = $('#' + to_name);
        this.$vi = $('#' + view_id);
    },
    load: function() {
        $(document).on('click', '#selectparase .modal-footer a', function() {
            VKParase.$vi.modal('hide');
        });
        $(document).on('click', '#vk_parase_item tbody tr', function() {
            var oldContent = '';
            var tag = VKParase.$tn.get(0).tagName.toLowerCase();
            if (tag === 'textarea') {
                oldContent = VKParase.$tn.val();
            } else {
                oldContent = VKParase.$tn.find('textarea').val();
            }
            var newsign = oldContent + $(this).attr('item_name');
            if (tag === 'textarea') {
                VKParase.$tn.val(newsign);
                VKParase.$tn.text(newsign);
                VKParase.$tn.focus();
            } else {
                VKParase.$tn.find('textarea').val(newsign);
                VKParase.$tn.find('textarea').text(newsign);
                VKParase.$tn.find('textarea').focus();
            }
            VKParase.addParaseTimes($(this).attr('item_id'));
            VKParase.$vi.modal('hide');
        });
        //搜索
        $('#selectparase .search-query').keyup(function() {
            var keyword = $(this).val();
            VKParase.urlParams.keyword = keyword;
            VKParase.initItem();
        });
    },
    initItem: function() {
        var parases = VKParase.getJsonData();
        var htmlStr = '';
        if (parases) {
            $.each(parases, function(id, content) {
                htmlStr += '<tr item_id="' + id + '" item_name="' + content + '"><td style="text-align:center;"><span class="pull-left icon-checkmark-3" style="display:none;color: #468847;"></span>' + content + '</td></tr>';
            });
            $('#vk_parase_item tbody').html(htmlStr);
        }
    },
    getJsonData: function() {
        var jsonData = '';
        $.ajax({
            type: 'POST',
            url: VKParase.baseUrl,
            data: VKParase.urlParams,
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
    addParaseTimes: function(id) {
        $.ajax({
            type: 'POST',
            url: VKParase.baseUrl,
            data: {
                'id': id
            },
            async: false
        });
    },
    show: function() {
        this.initItem();
        this.$vi.modal('show');
    }
};


function vk_selectparase(to_id, to_name, view_id) {
    VKParase.init(to_id, to_name, view_id);
    VKParase.show();
}