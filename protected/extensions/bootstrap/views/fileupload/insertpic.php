<!-- The file upload form used as target for the file upload widget -->
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    <li class="template-download template-insert fade">
        <div class="return-message">
            {% for (var i=0, file; file=o.files[i]; i++) { %} 
            
            <!--                        {% if (file.error) { %}
                                        <td></td>
                                        <td class="name"><span>{%=file.name%}</span></td>
                                        <td class="size"  colspan="2"><span>{%=o.formatFileSize(file.size)%}</span></td>
                                    <span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}
                                    {% } else { %}
                                        <td class="preview">{% if (file.thumbnail_url) { %}
                                            <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}" height="100" width="100"></a>
                                        {% } %}</td>
                                        <td class="size"  colspan="2"><span>{%=o.formatFileSize(file.size)%}</span></td>
                                        <td class="success"  colspan="2"><span class="label label-success">{%=locale.fileupload.success%}</span> {%=locale.fileupload.successes.tips%}</td>
                                        <td colspan="2"></td>
                        
                                 {% } %}-->
            {%  if (file.error) { %}
            <a href="javascript:;" title="{%=file.name%}">{%=file.name%}</a>
            <div class="btn btn-warning btn-insert" style="display: none;">
                <span>格式错误</span>
            </div>
            <div class="delete-pic" style="display: none;">
                <div class="btn btn-danger btn-delete-pic">
                    <span>{%=locale.fileupload.destroy%}</span>
                </div>
            </div>
            {% } else { %}
            {% if (file.thumbnail_url) { %}<a href="javascript:;" title="{%=file.name%}"><img src="{%=file.thumbnail_url%}"  title="{%=file.name%}"></a>      {% } %}
            <button type="button" class="btn btn-info btn-insert" style="display: none;" data-url="{%=file.thumbnail_url%}" data-ckeditor="{%=file.ckeditor%}">
                <span>插入图片</span>
            </button>
            <!--        </td>-->
            <div class="delete-pic" style="display: none;">
                <div class="btn btn-danger btn-delete-pic">
                    <span>{%=locale.fileupload.destroy%}</span>
                </div>
            </div>
            {% } %}

            {% } %}
        </div>
    </li>
</script>