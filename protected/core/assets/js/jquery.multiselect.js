/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($){
    var MultiSelect = {
        init : function(options){
            var id = this.attr("id");
            var opt = $.extend({
                "up":id+"_btn_up",
                "down":id+"_btn_down",
                "left":id+"_btn_moveleft",
                "right":id+"_btn_moveright",
                "selectall":id+"_btn_select_all",
                "noselectall":id+"_btn_noselect_all",
                "select":id+"_select",
                "noselect":id+"_noselect"
            }, options || {});
            $(document).on("click", "#"+opt.up, function(){
                $("#"+opt.select+" option:selected:last").after($("#"+opt.select+" option:selected:first").prev().clone());
                $("#"+opt.select+" option:selected:first").prev().remove();
            });

            $(document).on("click", "#"+opt.down, function(){
                $("#"+opt.select+" option:selected:first").before($("#"+opt.select+" option:selected:last").next().clone());
                $("#"+opt.select+" option:selected:last").next().remove();
            });

            $(document).on("click", "#"+opt.right, function(){
                $("#"+opt.noselect).append($("#"+opt.select+" option:selected").clone());
                $("#"+opt.select+" option:selected").remove();
            });

            $(document).on("click", "#"+opt.left, function(){
                $("#"+opt.select).append($("#"+opt.noselect+" option:selected").clone());
                $("#"+opt.noselect+" option:selected").remove();
            });

            $(document).on("click", "#"+opt.selectall, function(){
                $("#"+opt.select+" option").each(function(){
                    this.selected = true;
                });
            });

            $(document).on("click", "#"+opt.noselectall, function(){
                $("#"+opt.noselect+" option").each(function(){
                    this.selected = true;
                });
            });
        }
    };

    $.fn.MultiSelect = function(method){
        if(MultiSelect[method]){
            return MultiSelect[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === "object" || !method) {
            return MultiSelect.init.apply(this, arguments);
        } else {
            $.error("Method " + method + " does not exist on jQuery.MultiSelect");
            return false;
        }
    };

})(jQuery);