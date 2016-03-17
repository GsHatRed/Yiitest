<!-- The file upload form used as target for the file upload widget -->
<style>
    .btn-add {
        margin:0px auto;
        padding: 0;
        padding-top: 80px;
        width: 120px;
        float: none;
        height: 30px;

    }
    .message-info {
        width: 120px;
        float: left;
        height: 30px;
        padding: 0;
        margin: 0;
        font-size: 16px;
        color: #AAAAAA;
    }
</style>
<?php echo CHtml::beginForm($this->url, 'post', $this->htmlOptions); ?>
<div class="fileupload-buttonbar">
    <div class="btn-add">
        <!-- The fileinput-button span is used to style the file input field as button -->
        <span class="btn btn-success fileinput-button"> <i class="icon-plus icon-white"></i> <span>添加文件</span>
            <?php
            if ($this->hasModel()) :
                echo CHtml::activeFileField($this->model, $this->attribute, $this->htmlOptions['multiple'] ? array('multiple'=>'multiple'):array()) . "\n";
            else :
                echo CHtml::fileField($name, $this->value, $this->htmlOptions['multiple'] ? array('multiple'=>'multiple'):array()) . "\n";
            endif;
            ?>
        </span>
        <div class="message-info clearfix">可上传多个文件</div>
        <button type="submit" class="btn btn-primary start" style="display:none">
            <i class="icon-upload icon-white"></i>
            <span>开始上传</span>
        </button>
        <button type="reset" class="btn btn-warning cancel" style="display:none">
            <i class="icon-circle icon-white"></i>
            <span>取消上传</span>
        </button>
        <!--        <button type="button" class="btn btn-danger delete">
                    <i class="icon-remove icon-white"></i>
                    <span>删除</span>
                </button>
                <input type="checkbox" class="toggle">-->
    </div>
    <div class="span5 fileupload-progress fade" style="display:none">
        <!-- The global progress bar -->
        <div class="progress progress-success progress-striped active" role="progressbar">
            <div class="bar" style="width:0%;"></div>
        </div>
        <!-- The extended global progress information -->
        <div class="progress-extended">&nbsp;</div>
    </div>
</div>
<!-- The loading indicator is shown during image processing -->
<div class="fileupload-loading"></div>
<br>
<!-- The table listing the files available for upload/download -->
<div class="row-fluid">
    <table class="table table-striped">
        <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
    </table>
</div>
<?php echo CHtml::endForm(); ?>