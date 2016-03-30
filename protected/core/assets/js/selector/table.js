


var VKTable = {
    
    baseUrl : null,
    viewId : null,
    
    init : function(to_id, database) {
        this.$ti = $('#'+to_id);
        this.$view = $('#'+this.viewId);
        this.db = database;
    }
    
    , load : function() {
        $(document).on('click', "#vk_table_tbody tr", function(){
            if($(this).prop('className').indexOf('selected') > 0)
                $(this).find('input[type=checkbox]').prop('checked', false).click().prop('checked', false);
            else
                $(this).find('input[type=checkbox]').prop('checked', true).click().prop('checked', true);
        });

        $(document).on('click', "#vk_table_select_all", function(){
            var checked = this.checked;
            $("#vk_table_tbody input[type=checkbox]").each(function(){
                this.checked = checked; 
                if(checked===false) {
                    $(this).prop('checked', false).click().prop('checked', false);
                } else {
                    $(this).prop('checked', true).click().prop('checked', true);
                }
            });
        });

        $(document).on('click', "#vk_table_tbody input[type=checkbox]", function(event) {
            var checked = $(this).prop('checked');
            if(checked === true) {
                VKTable.add($(this).val());
                $(this).parents('tr').addClass('selected');
            } else {
                VKTable.del($(this).val());
                $(this).parents('tr').removeClass('selected');
            }

            $("#vk_table_select_all").prop('checked', $("#vk_table_tbody input[type=checkbox]").length === $("#vk_table_tbody input[type=checkbox]:checked").length);
            event.stopPropagation();
        });
        
        $(document).on('click', '#selecttable .modal-footer a', function(){
            $('#selecttable').modal('hide');
        });
    }
    
    ,get : function() {
        var url = this.baseUrl + '?db=' + this.db;
        $.getJSON(url, function(data){
            if(data.list && $('#vk_table_tbody').length > 0) {
                $('#vk_table_tbody').html(data.list);
                VKTable.initItem();
            }
        });
    }
    
    ,add : function(item_id) {
        if(this.$ti.val().indexOf(item_id + ',') !== 0 && this.$ti.val().indexOf(','+item_id+',') <= 0) {
            this.$ti.attr('value', function(){
                return this.value + item_id + ',';
            });
        }
    }
    
    ,del : function(item_id) {
        if(this.$ti.val().indexOf(item_id + ',') === 0 || this.$ti.val().indexOf(','+item_id+',') > 0) {
            if(this.$ti.val().indexOf(item_id+',') === 0)
                this.$ti.val(this.$ti.val().substr(item_id.length + 1));
            else
                this.$ti.val(this.$ti.val().replace(','+item_id+',', ','));
        }
    }
    
    ,initItem: function() {
        var idf = this.$ti;
        $('#vk_table_tbody tr').each(function(){
            var checkbox = $(this).find('input[type=checkbox]');
            var item_id = checkbox.val();

            if(idf.val().indexOf(item_id+',')===0 || idf.val().indexOf(','+item_id+',')>0 || idf.val()===item_id) {
                $(this).addClass('selected');
                checkbox.prop('checked', true);
            } else {
                $(this).removeClass('selected');
                checkbox.prop('checked', false);
            }
        });
        $("#vk_table_select_all").prop('checked', $("#vk_table_tbody input[type=checkbox]").length === $("#vk_table_tbody input[type=checkbox]:checked").length);
    }
    
    ,show : function() {
        this.get();
        this.$view.modal('show');
    }
    
};

function vk_selecttable(to_id, database) {
    VKTable.init(to_id, database);
    VKTable.show();
}

function vk_del_selecttable(to_id) {
    $('#'+to_id).val('');
}