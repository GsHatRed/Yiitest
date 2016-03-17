<!-- The template to display files available for upload -->

<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-upload fade template-insert">
        <div class="return-message">
            {% if (file.error) { %}
            <div class="error" ><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</div>
            {% } else if (o.files.valid && !i) { %}

            <div class="preview"><span class="fade"></span></div>
            <div class="progress-bar">
                <div class="progress progress-success progress-striped active"><div class="bar" style="width:0%;"></div></div>
            </div>

            <div class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>{%=locale.fileupload.start%}</span>
                </button>
                {% } %}</div>

            {% } %}

        </div>
    </li>
    {% } %}
</script>