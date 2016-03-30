var CodeFilterUtil = {
    formatIcon : function(item, container, query) {
        var originalOption = item.element;
        var icon = $(originalOption).attr('data-icon');
        var ret = item.text;
        if(icon)
           ret = "<i class='" + icon + "'/></i>" + ret;
        return ret;
    },
    escapeMarkup: function(m) { 
        return m; 
    },
    changeUrl : function(e) {
        var url = $(e.added.element).attr('data-url');
        if(url!==undefined)
            window.location = url;
    }
}

