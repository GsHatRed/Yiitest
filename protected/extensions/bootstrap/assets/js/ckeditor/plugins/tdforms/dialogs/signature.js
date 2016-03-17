/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'signature', function( editor ) {
    var id = '';
    if(editor.getSelection().getSelectedElement())
        id = editor.getSelection().getSelectedElement().getAttribute( 'name' ).split('_')[1];
    else
        id = 'new';
    return {
        title: editor.lang.tdforms.signature.dialogTitle,
        minWidth: 350,
        minHeight: 220,
        onShow: function() {

            delete this.signature;

            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "img" && element.getAttribute('class') == 'signature') {
                this.signature = element;
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor,
            element = this.signature,
            fieldId='',
            widget_json = '',
            field_name = this.getValueOf( 'info', 'title'),
            field_type = 'signature',
            isInsertMode = !element,
            dv = "";
            $(window.frames["signatureiframe"].document).find(".ms-selection .ms-selected").each(function(){
                var id = $(this).attr('id').replace(/_/g,'').split('-')[0];
                dv += id+',';
            });
            dv = dv.substr(dv.length-1) == ',' ? dv.substr(0,dv.length-1) : dv;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"class":' + '"' + field_type + '",'
            + '"value":' + '"' + dv + '"' + '}}';

            if ( isInsertMode ) {
                editor = this.getParentEditor();
                element = editor.document.createElement( 'img' );
                element.setAttribute( 'src', baseUrl+'/images/form/signature.png');
                element.setAttribute( 'class', 'signature' );
                element.setAttribute( 'title', field_name);
                element.setAttribute( 'value', dv);

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
                if(fieldId == 'repeat'){
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                } else {
                    element.setAttribute( 'name', "data_"+fieldId );
                    element.setAttribute( 'id', 'data_'+fieldId);
            }
            }


            if ( !isInsertMode ) {
                element.setAttribute( 'value', dv);
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

            if ( isInsertMode ) {
                editor.insertElement( element );
            }
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
                        element.setAttribute( 'title', this.getValue() );
                }
            },
            {
                type: 'html',
                html: "<div><p style='font-size:small'>" + CKEDITOR.tools.htmlEncode( editor.lang.common.signatureTip ) + "</p></div>"
            },
            {
                onShow: function() {
                    var selement = editor.getSelection().getSelectedElement();
                    var eclass = selement ? selement.getAttribute( 'class' ) : '';
                    id =  selement ? selement.getAttribute( 'name' ).split('_')[1] : '';
                    var a = g_loadFormFieldURL;
                    var b = a.replace('_formID',g_form_id);
                    b = b.replace('_randomNum',Math.floor(Math.random()*100000));
                    if(id && eclass == 'signature')
                        b = b.replace('_id',id);
                    else
                        b = b.replace('_id','new');
                    $("#signatureiframe").attr("src",b);
                },
                type: 'html',
                html:'<iframe style="width:500px;height:220px" id="signatureiframe" name="signatureiframe" src=""></iframe>'
            }
            ]
        }
        ]
    };
});