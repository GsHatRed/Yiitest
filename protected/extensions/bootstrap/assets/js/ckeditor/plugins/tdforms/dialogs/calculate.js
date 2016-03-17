/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'calculate', function( editor ) {
    
    return {
        title: editor.lang.tdforms.calculate.dialogTitle,
        minWidth: 400,
        minHeight: 300,
        onShow: function() {
            delete this.calculate;
            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "textarea" && element.getAttribute('class') == 'calculate') {
                this.calculate = element;
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor,
            element = this.calculate,
            widget_json = '',
            fieldId='',
            field_name = this.getValueOf( 'info', 'title' ),
            fontSize = this.getValueOf('info','fontSize'),
            height = this.getValueOf('info','wheight'),
            width = this.getValueOf('info','wwidth'),
            dv = this.getValueOf( 'info', 'value'),
            field_type = 'calculate',
            isInsertMode = !element;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"class":' + '"' +field_type + '",'
            + '"height":' + '"' + height + '",'
            + '"width":' + '"' + width + '",' + '"value":' + '"' + dv  + '",' + '"fontSize":' + '"' + fontSize + '"' +'}}';
            if ( isInsertMode ) {
                editor = this.getParentEditor();
                element = editor.document.createElement( 'textarea' );
                element.setAttribute( 'class', field_type );
                fieldId = $.ajax({
                    type: "post",
                    url: g_requestURL,
                    data: {
                        fieldInfo:widget_json,
                        formID:g_form_id
                    },
                    cache: false,
                    async: false
                }).responseText;
                if(fieldId === 'repeat'){
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                } else {
                    element.setAttribute( 'name', 'data_'+fieldId );
                    element.setAttribute( 'id', 'data_'+fieldId);
                    if(fontSize)
                        $(element.$).css({
                            "font-size":fontSize+"px"
                        });
                    if(height)
                        $(element.$).css({
                            "height":height+"px"
                        });
                    if(width)
                        $(element.$).css({
                            "width":width+"px"
                        });
                }
            }

            if ( isInsertMode )
                editor.insertElement( element );

            if( !isInsertMode ) {
                fieldId = element.getAttribute( 'name' ).split('_')[1];
                fieldId = $.ajax({
                    type: "post",
                    url: g_requestURL,
                    data: {
                        fieldInfo:widget_json,
                        formID:g_form_id,
                        fieldId:fieldId
                    },
                    cache: false,
                    async: false
                }).responseText;
                if(fieldId == 'repeat'){
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                }
            }
            this.commitContent( element );
        },
        contents: [
        {
            id: 'info',
            elements: [
            {
                id: 'title',
                type: 'text',
                label: editor.lang.common.controlName,
                validate:CKEDITOR.dialog.validate.notEmpty( editor.lang.common.validateControlFailed ),
                'default': '',
                accessKey: 'N',
                setup: function( element ) {
                    this.setValue( element.getAttribute( 'title' ) || '' );
                },
                commit: function( element ) {
                    if ( this.getValue() )
                        element.setAttribute( 'title', this.getValue());
                }
            },
        
            /******************************************************/
             {
                id: 'value',
                type: 'textarea',
                label: editor.lang.tdforms.calculate.computational,
                'default': '',
                setup: function( element ) {
                    this.setValue( element.$.defaultValue );
                },
                commit: function( element ) {
                    element.$.value = element.$.defaultValue = this.getValue();
                },
                onShow:function(){
                    var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                    if(element)
                        this.setValue( element.$.defaultValue );
                },
            },
            {
                type: 'html',
                html: "<div id='appendjs'><tr id='cke_dialog_ui_hbox' onclick='javascript:read_more()'><td style='color:blue'>查看计算公式填写说明</td></tr></div>",
                onShow: function() {
                    var html = ' <script>function read_more() {'+
                    'if(desc_text.style.display==""){'+
                        'desc_text.style.display="none";'+
                        'document.getElementById("cke_dialog_ui_hbox").innerHTML="<td style=\'color:blue\'>查看计算公式填写说明</td>";'+
                    '}else{'+
                        'desc_text.style.display="";'+
                        'document.getElementById("cke_dialog_ui_hbox").innerHTML="<td style=\'color:blue\'>隐藏计算公式填写说明</td>";'+
                    '}}</script>';
                    $("#appendjs").append(html);                   
                }
            },
            {
                type: 'html',
                html: "<div><tr id='desc_text' style='display:none'><td>计算公式支持+ - * / ^和英文括号以及特定计算函数，例如：(数值1+数值2)*数值3-ABS(数值4)<br>其中数值1、数值2等为表单控件名称。<br>当前版本所支持的计算函数：<br>1、MAX(数值1,数值2,数值3...) 输出最大值,英文逗号分割;<br>2、MIN(数值1,数值2,数值3...) 输出最小值,英文逗号分割;<br>3、ABS(数值1) 输出绝对值;<br>4、MOD(数值1,数值2) 计算数值1和数值2的余数;<br>5、AVG(数值1,数值2,数值3) 输出平均值;<br>6、RMB(数值1) 输出人民币大写形式，数值范围0～9999999999.99;</td></tr></div>"
            },
            {
                id:'fontSize',
                type:'text',
                labelLayout: 'horizontal',
                label: editor.lang.tdforms.calculate.fontSize,
                validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                setup: function( element ) {
                    this.setValue( element.$.style.fontSize.substr(0,element.$.style.fontSize.length - 2) );
                }
            },
            {
                type: 'text',
                id: 'wheight',
                labelLayout: 'horizontal',
                label: editor.lang.tdforms.calculate.fontSize,
                validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                setup: function(element) {
                    this.setValue(element.getAttribute('w_height') ? element.getAttribute('w_height') : '');
                }
            },
            {
                type: 'text',
                labelLayout: 'horizontal',
                id: 'wwidth',
                label: editor.lang.widget.wwidth,
                validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                setup: function(element) {
                    this.setValue(element.getAttribute('w_width') ? element.getAttribute('w_width') : '');
                }
            },
            ]
        }
        ]
    };
});
