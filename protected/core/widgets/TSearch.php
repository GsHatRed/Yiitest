<?php

/**
 * TSearch class file.
 * 
 * @author ZMM
 *
 */

/*
 * 通用搜索组件
 * 
 * <pre>
 * $this->widget('core.widgets.Tsearch', array(
 *       'viewId' => 'book-grid',   //设置为数组的时候，此时设置的格式为 :array('grid'=>'book-grid','kanban'=>'book-kanban','summary'=>'book-summary');键名为传递过去的viewType值，键值为相应的listview对应的id值
 *       'viewType' => 'grid',
 *       'model' => $model, //传递过去的model
 *       'ajaxUpdate' => true,//是否ajax更新，可以不设置，默认为true,此时可是设置viewUrl设置更新的action ,不设置的时候为当前action,若设置为false的时候和searchUrl一起使用
 *       'displaySearchModal'  => true, //是否调用高级搜索，可以不设置，默认为true,通过设置searchUrl从而设置查询路径，否则默认为当前页面
 *       'displayExport'  => false, //是否开启导出，可以不设置，默认为false,通过设置exportUrl从而设置导出路径
 *       'formAttributes' => array("name","number"),   //高级搜索表单的字段名，字段名可以用数组替换
 *       'attributes' => array("name","number"),   //搜索下拉框的字段名，字段名可以用数组替换，此时此字段就是已知的值
 *       'htmlOptions'=>array('class'=>'pull-left')   //设置属性值，默认自带了一个class值为"pull-right"
 * ));
 * </pre>
 *
 */
class TSearch extends CInputWidget {
    // Field types.

    const TYPE_SELECT = 'select';
    const TYPE_SELECT_USER = 'select_user';
    const TYPE_SELECT_STUDENT = 'select_student';
    const TYPE_SELECT_CLASS = 'select_class';
    const TYPE_SELECT_PARENTS = 'select_parents';
    const TYPE_SELECT_ORG = 'select_org';
    const TYPE_SELECT_ROLE = 'select_role';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO = 'radio';
    const TYPE_DATETIME = 'datetime';
    const TYPE_DATERANGE = 'daterange';
    const TYPE_DATE = 'date';
    const TYPE_TEXT = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_HIDDEN = 'hidden';

    /**
     * @var string 视图列表的id
     */
    public $viewId;

    /**
     * @var array 下拉选项属性
     */
    public $attributes = array();

    /**
     * @var array 高级搜索字段属性
     */
    public $formAttributes = array();

    /**
     * @var array HTML属性选项
     */
    public $htmlOptions = array();

    /**
     * @var array model
     */
    public $model;

    /**
     *  @var string 视图类型
     */
    public $viewType = "grid";

    /**
     * @var boolean  是否开启高级搜索
     */
    public $displaySearchModal = true;

    /**
     * @var boolean  是否开启导出
     */
    public $displayExport = false;

    /**
     * @var string  导出路径
     */
    public $exportUrl;

    /**
     * @var boolean  是否动态更新
     */
    public $ajaxUpdate = true;
//
    /**
     * @var string 查询路径
     */
    public $searchUrl;

    /**
     * @var string 加载url
     */
    public $viewUrl;

    /**
     * ### .init()
     *
     * Initializes the widget.
     */
    public function init() {
        if (($this->viewId === null) || (empty($this->viewId)))
            throw new CException(Yii::t('zii', 'The property "viewId" cannot be empty.'));
        if (empty($this->model))
            throw new CException(Yii::t('zii', 'The property "model" cannot be empty.'));
        if (empty($this->attributes))
            throw new CException(Yii::t('zii', 'The property "attributes" cannot be empty.'));
        $classes = array('pull-right');
        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->htmlOptions['class']))
                $this->htmlOptions['class'] .= ' ' . $classes;
            else
                $this->htmlOptions['class'] = $classes;
        }
        parent::init();
    }

    public function run() {
        $model = $this->model;
        $id = !empty($this->htmlOptions['id']) ? $this->htmlOptions['id'] : $this->getId();
        $this->htmlOptions['id'] = !empty($this->htmlOptions['id']) ? $this->htmlOptions['id'] : $this->getId();
        $viewType = $this->viewType;
        $viewUrl = $this->viewUrl;
        $viewId = is_array($this->viewId) ? $this->viewId[$viewType] : $this->viewId;
        $modelLabels = $model->attributeLabels();
        $attributeNameSring = "";
        $totalArray = Array();
        for ($i = 0; $i < count($this->attributes); $i++) {
            if (is_array($this->attributes[$i])) {
                
            } else {
                $totalArray[] = $this->attributes[$i];
            }
        }
        for ($i = 0; $i < count($totalArray); $i++) {
            $attributeString = $totalArray[$i];
            if ($i != count($totalArray) - 1) {
                $attributeNameSring .= $modelLabels[$attributeString] . '、';
            } else {
                $attributeNameSring .= $modelLabels[$attributeString];
            }
        }
        Yii::app()->getClientScript()->registerCssFile(Yii::app()->core->getAssetsUrl() . '/css/search.css');
        $this->registerClientScript($id, $viewType, $viewId, $viewUrl);
        if ($this->displaySearchModal)
            $this->_genAdvSearchModal();
        echo CHtml::openTag('div', $this->htmlOptions);
        echo CHtml::openTag('div', array("class" => "smartsearch", "id" => "smartSearch"));
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'method' => 'GET',
            'id' => 'smartSearch_form_' . $id,
            'action' => $this->searchUrl,
                ));
        for ($i = 0; $i < count($this->attributes); $i++) {
            if (is_array($this->attributes[$i])) {
                $idArray = array("id" => empty($this->attributes[$i]['htmlOptions']['id']) ? "search_" . $this->attributes[$i]['name'] : $this->attributes[$i]['htmlOptions']['id']);
                if (is_array($this->attributes[$i]['htmlOptions'])) {
                    $htmlOptions = array_merge($this->attributes[$i]['htmlOptions'], $idArray);
                }
                $htmlOptions['class'] = "searchinput";
                echo $form->hiddenField($model, $this->attributes[$i]['name'], $htmlOptions);
            } else {
                echo $form->hiddenField($model, $this->attributes[$i], array("id" => "search_" . $this->attributes[$i]));
            }
        }
        echo CHtml::tag('i', array("class" => "search_icon icon-search-2"), "");
        echo "<input name='subject_" . $id . "' class='searchinput' id='subject_" . $id . "'  style='color: #a0a0a0;' placeholder='根据" . $attributeNameSring . "搜索......'/>";
        echo CHtml::tag('i', array("class" => "search_icon icon-arrow-down-2"), "");
        echo CHtml::openTag('ul', array("class" => "search-menu dropdown-menu"));
        for ($i = 0; $i < count($this->attributes); $i++) {
            if (!is_array($this->attributes[$i])) {
                $attribute = $this->attributes[$i];
                $attributeName = $modelLabels[$attribute];
                $class = $i == 0 ? "search-attribute active" : "search-attribute";
                echo CHtml::openTag('li', array('class' => $class));
                echo "<a data-type='search_" . $this->attributes[$i] . "'><span class='word-first'>包含<span class='search-word'></span></span>的" . $attributeName . "</a>";
                echo CHtml::closeTag('li');
            }
        }
        if ($this->displaySearchModal) {
            echo "<li class='divider'></li><li class=\"search-attribute\"><a action-type=\"advsearch\">高级搜索...</a></li>";
        }
        echo CHtml::closeTag('ul');
        $this->endWidget();
        echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
    }

    /**
     * 注册JS
     */
    protected function registerClientScript($id, $viewType, $viewId, $viewUrl) {

        $clientScript = Yii::app()->clientScript;
        $clientScript->registerScript(__CLASS__ . $this->getId(), '

          $("#subject_' . $id . '").focus(function(){
              $(this).next().attr("class","search_icon icon-enter-4");
              $("#smartSearch").find(".dropdown-menu").show();
                if($.trim($(this).val())!=""){
                     $("#smartSearch").find("input:not(.searchinput)").val("");
                     var id = $("#smartSearch .dropdown-menu li.active").find("a").attr("data-type");
                     $("#smartSearch").find("#"+id).val($(this).val());
                      $("#smartSearch .dropdown-menu li").find(".word-first").html("包含<span class=\'search-word\'></span>");
                      $("#smartSearch .dropdown-menu li").find(".search-word").text($(this).val());
                }else{
                         $("#smartSearch").find("input:not(.searchinput)").val("");
                      $("#smartSearch .dropdown-menu li").find(".word-first").text("查看所有");
                }
            });
           document.onkeydown = changSelect;
           function changSelect() {
               if($("#smartSearch .dropdown-menu").is(":visible")==true){
                    if(event.keyCode == 38){
                       var dataType =  $("#smartSearch .dropdown-menu li.active").prev().find("a").attr("data-type");
                       $("#smartSearch  .dropdown-menu").find("[data-type="+dataType+"]").parent().siblings().removeClass("active");
                       $("#smartSearch  .dropdown-menu").find("[data-type="+dataType+"]").parent().addClass("active");
                     }else if(event.keyCode == 40){
                       var dataType =  $("#smartSearch .dropdown-menu li.active").next().find("a").attr("data-type");
                       $("#smartSearch  .dropdown-menu").find("[data-type="+dataType+"]").parent().siblings().removeClass("active");
                       $("#smartSearch  .dropdown-menu").find("[data-type="+dataType+"]").parent().addClass("active");
                    }else if(event.keyCode == 13){
                        $("#smartSearch .dropdown-menu li.active").trigger("click");
                    }
                }
           }
           $("#subject_' . $id . '").live("click", function(){
               $("#smartSearch").find(".dropdown-menu").show();
               if($.trim($(this).val())!=""){
                     $("#smartSearch").find("input:not(.searchinput)").val("");
                      var id = $("#smartSearch .dropdown-menu li.active").find("a").attr("data-type");
                     $("#smartSearch").find("#"+id).val($(this).val());
                    $("#smartSearch .dropdown-menu li").find(".word-first").html("包含<span class=\'search-word\'></span>");
                    $("#smartSearch .dropdown-menu li").find(".search-word").text($(this).val());
               }else{
                    $("#smartSearch").find("input:not(.searchinput)").val("");
                    $("#smartSearch .dropdown-menu li").find(".word-first").text("查看所有");
                }
                  return false;
            })
           $("#subject_' . $id . '").bind("keyup", function(event){
                if(event.keyCode!=13){
               $(this).next().attr("class","search_icon icon-enter-4");
               $("#smartSearch").find(".dropdown-menu").show();
               if($.trim($(this).val())!=""){
                    $("#smartSearch").find("input:not(.searchinput)").val("");
                     var id = $("#smartSearch .dropdown-menu li.active").find("a").attr("data-type");
                     $("#smartSearch").find("#"+id).val($(this).val());
                    $("#smartSearch .dropdown-menu li").find(".word-first").html("包含<span class=\'search-word\'></span>");
                    $("#smartSearch .dropdown-menu li").find(".search-word").text($(this).val());
               }else{
                 $("#smartSearch").find("input:not(.searchinput)").val("");
                    $("#smartSearch .dropdown-menu li").find(".word-first").text("查看所有");
                }
                  return false;
                  }
            })
            $("#smartSearch .icon-arrow-down-2").live("click",function(){
                 $("#smartSearch").find(".dropdown-menu").show();
                 if($.trim($("#subject_' . $id . '").val())!=""){
                      $("#smartSearch").find("input:not(.searchinput)").val("");
                       var id = $("#smartSearch .dropdown-menu li.active").find("a").attr("data-type");
                     $("#smartSearch").find("#"+id).val($(this).val());
                   $("#smartSearch .dropdown-menu li").find(".word-first").html("包含<span class=\'search-word\'></span>");
                   $("#smartSearch .dropdown-menu li").find(".search-word").text($("#subject_' . $id . '").val());
               }else{
                    $("#smartSearch").find("input:not(.searchinput)").val("");
                    $("#smartSearch .dropdown-menu li").find(".word-first").text("查看所有");
               }
                  return false;
            });
            $("#smartSearch .icon-enter-4").live("click",function(){
                 $(this).attr("class","search_icon icon-arrow-down-2");
                 $("#smartSearch").find(".dropdown-menu").show();
                 if($.trim($("#subject_' . $id . '").val())!=""){
                   $("#smartSearch .dropdown-menu li").find(".word-first").html("包含<span class=\'search-word\'></span>");
                   $("#smartSearch .dropdown-menu li").find(".search-word").text($("#subject_' . $id . '").val());
               }else{
                   $("#smartSearch").find("input:not(.searchinput)").val("");
                    $("#smartSearch .dropdown-menu li").find(".word-first").text("查看所有");
               }
                  return false;
            });
            $(document).click(function() {
               $("#smartSearch").find(".dropdown-menu").hide();
               $("#subject_' . $id . '").next().attr("class","search_icon icon-arrow-down-2");
            });
            $("#smartSearch .dropdown-menu li.search-attribute").live("click",function(){
            if(typeof($(this).children().attr("action-type"))=="undefined"){
                 var id = $(this).children().attr("data-type");
                 var value = $("#subject_' . $id . '").val();
                 $("#smartSearch").find("input:not(.searchinput)").val("");
                 $("#"+id).val(value);

                 $("#smartSearch form").submit();
                   $("#smartSearch").find(".dropdown-menu").hide();
                 return false;
                 }else{
                $("#advSearchModal").modal("show");
                $("#advSearchModal_form").get(0).reset();
                $(this).parents(".search-menu").hide();
                 }
            });
            $("#smartSearch .dropdown-menu li.search-attribute").live("mouseenter",function(){
                  $(this).addClass("active");
                  $(this).siblings().removeClass("active");
            });
            $("#advSearchModal form").submit(function(){
                var ajaxUpdate = "' . $this->ajaxUpdate . '";
               $("#smartSearch").find(".dropdown-menu").hide();
               $("#subject_' . $id . '").next().attr("class","search_icon icon-arrow-down-2");
               $("#advSearchModal").modal("hide");
               if(($(this).find("#advSearchType").val()=="1") && (ajaxUpdate == 1)){
               var viewType = "' . $viewType . '";
               var viewUrl = "' . $viewUrl . '"
               if(viewType == "grid"){
                   $.fn.yiiGridView.update("' . $viewId . '", {
		                data: $(this).serialize(),
                        url : viewUrl
	            });
                }else if(viewType == "summary") {
                   $.fn.yiiListView.update("' . $viewId . '", {
                       data: $(this).serialize(),
                       url : viewUrl
                  })
                }else {
                    $.fn.kanbanView.update("' . $viewId . '", {
                       data: $(this).serialize(),
                       url : viewUrl
                  })
                }
               return false;
               }

            });
            $("#advSearchModal").find("[data-type=\'submit\']").live("click",function(){
               $("#advSearchType").val("1");
               $("#advSearchModal form").submit();
            });
             $("#advSearchModal").find("[data-type=\'export\']").live("click",function(){
               $("#advSearchType").val("2");
               $("#advSearchModal form").attr({"action":"' . $this->exportUrl . '"});
               $("#advSearchModal form").submit();
            });
            $("#smartSearch form").submit(function(){
               var ajaxUpdate = "' . $this->ajaxUpdate . '";
               $("#smartSearch").find(".dropdown-menu").hide();
               $("#subject_' . $id . '").next().attr("class","search_icon icon-arrow-down-2");
            if(ajaxUpdate == 1){
               var viewType = "' . $viewType . '";
               var viewUrl = "' . $viewUrl . '"
               if(viewType == "grid"){
               $.fn.yiiGridView.update("' . $viewId . '", {
		                data: $(this).serialize(),
                        url : viewUrl
	            });
                }else if(viewType == "summary"){
                   $.fn.yiiListView.update("' . $viewId . '", {
                       data: $(this).serialize(),
                       url : viewUrl
                  })
                }else{
                    $.fn.kanbanView.update("' . $viewId . '", {
                       data: $(this).serialize(),
                       url : viewUrl
                  })
                }
               return false;
              }
            });
');
    }

    /**
     *
     * @todo 高级搜索modal
     */
    private function _genAdvSearchModal() {
        $this->beginWidget('bootstrap.widgets.TbModal', array('id' => "advSearchModal"));
        echo CHtml::openTag('div', array('class' => 'modal-header'));
        echo CHtml::tag("a", array('class' => 'close', 'data-dismiss' => 'modal'), ×);
        echo CHtml::tag('h4', array('style' => 'height:20px;width:30%;display: inline-block;'), "高级搜索");
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'modal-body clearfix', 'style' => 'max-height:200px !important;'));
        $this->renderSearchForm();
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'modal-footer', 'style' => 'text-align:center'));
        $this->widget('bootstrap.widgets.TbButton', array(
            'type' => 'danger',
            'label' => '确定',
            'buttonType' => 'button',
            'htmlOptions' => array("data-type" => "submit", "style" => "margin-right:4px;"),
        ));
        if ($this->displayExport) {
            $this->widget('bootstrap.widgets.TbButton', array(
                'label' => '导出',
                'type' => 'info',
                'buttonType' => 'button',
                'htmlOptions' => array("data-type" => "export", "style" => "margin-right:4px;"),
            ));
        }
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button',
            'label' => '关闭',
            'htmlOptions' => array("data-dismiss" => "modal"),
        ));
        echo CHtml::closeTag('div');
        $this->endWidget();
    }

    /**
     *
     * @todo 高级搜索form
     */
    private function renderSearchForm() {
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'type' => 'horizontal',
            'method' => 'GET',
            'id' => 'advSearchModal_form',
            'action' => $this->searchUrl,
                ));
        $model = $this->model;
        echo CHtml::hiddenField("advSearchType");
        for ($i = 0; $i < count($this->formAttributes); $i++) {
            if (is_array($this->formAttributes[$i])) {
                $htmlOptions = array();
                $idArray = array();
                $type = $this->formAttributes[$i]["type"];
                $name = $this->formAttributes[$i]["name"];
                $data = $this->formAttributes[$i]['data'];
                if(($type!="select_user") && ($type!="select_org") && ($type!="select_role")&& ($type!="select_student")&& ($type!="select_parents")&& ($type!="select_class"))
                    $idArray = array("id" => empty($this->formAttributes[$i]['htmlOptions']['id']) ? "advSearchModal_" . $this->formAttributes[$i]['name'] : $this->formAttributes[$i]['htmlOptions']['id']);
                if (is_array($this->formAttributes[$i]['htmlOptions'])) {
                    $htmlOptions = array_merge($this->formAttributes[$i]['htmlOptions'], $idArray);
                }
                echo $this->createField($form, $model, $type, $name, $data, $htmlOptions);
                //       键值形式的选择器窗口，支持自定义选择
                //去掉兼容处理 fl 15-2-10
                /*
                if ($type == "select_user")
                    $this->widget('core.widgets.TSelectorModal', array('type' => 'user'));
                if ($type == "select_org")
                    $this->widget('core.widgets.TSelectorModal', array('type' => 'org'));
                if ($type == "select_role")
                    $this->widget('core.widgets.TSelectorModal', array('type' => 'role'));
                 */
            } else {
                     echo $form->textFieldRow($model, $this->formAttributes[$i], array("id" => "advSearchModal_" . $this->formAttributes[$i]));
            }
        }
        $this->endWidget();
    }

    /**
     * ### .createButton()
     *
     * Creates the button element.
     *
     * @return string the created button.
     */
    protected function createField($form, $model, $type, $name, $data, $htmlOptions) {
        switch ($type) {
            case self::TYPE_RADIO:
                return $form->radioButtonListInlineRow($model, $name, $data, $htmlOptions);
                break;
            case self::TYPE_SELECT:
                return $form->dropDownListRow($model, $name, CMap::mergeArray(array('' => ''), $data), $htmlOptions);
                break;
            case self::TYPE_DATETIME:
                return $form->datetimepickerRow($model, $name, CMap::mergeArray($htmlOptions,array('prepend' => '<i class="icon-calendar"></i>', 'options' => array('dateFormat' => 'yy-mm-dd'))));
                break;
            case self::TYPE_DATERANGE:
                return $form->dateRangeRow($model, $name, CMap::mergeArray($htmlOptions,array('prepend' => '<i class="icon-calendar"></i>', 'options' => array('format' => 'yyyy/MM/dd'))));
                break;
            case self::TYPE_DATE:
                return $form->datepickerRow($model, $name, CMap::mergeArray($htmlOptions,array('prepend' => '<i class="icon-calendar"></i>', 'options' => array('dateFormat' => 'yy-mm-dd'))));
                break;
            case self::TYPE_TEXT:
                return $form->textFieldRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_TEXTAREA:
                return $form->textAreaRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_HIDDEN:
                return $form->hiddenField($model, $name, $htmlOptions);
                break;
            case self::TYPE_SELECT_USER:
                $htmlOptions = empty($htmlOptions) ? array("type" => "user") : array_merge(array("type" => "user"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
            
            case self::TYPE_SELECT_ORG:
                $htmlOptions = empty($htmlOptions) ? array("type" => "org") : array_merge(array("type" => "org"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_SELECT_ROLE:
                $htmlOptions = empty($htmlOptions) ? array("type" => "role") : array_merge(array("type" => "role"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_SELECT_STUDENT:
                $htmlOptions = empty($htmlOptions) ? array("type" => "student") : array_merge(array("type" => "student"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
           case self::TYPE_SELECT_PARENTS:
                $htmlOptions = empty($htmlOptions) ? array("type" => "parents") : array_merge(array("type" => "parents"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_SELECT_CLASS:
                $htmlOptions = empty($htmlOptions) ? array("type" => "class") : array_merge(array("type" => "class"), $htmlOptions);
                return $form->selectorRow($model, $name, $htmlOptions);
                break;
            case self::TYPE_CHECKBOX:
                return $form->checkBoxListInlineRow($model, $name, $data, $htmlOptions);
                break;

            default:
            case self::TYPE_TEXT:
                return $form->textFieldRow($model, $name, $htmlOptions);
        }
    }

}

?>