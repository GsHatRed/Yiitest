/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'fileupload', function( editor ) {
    return {
        title: editor.lang.tdforms.fileupload.dialogTitle,
        minWidth: 350,
        minHeight: 150,
        onShow: function() {
            delete this.fileupload;

            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "img" && element.getAttribute( 'class' ) == 'fileupload' ) {
                this.fileupload = element;
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor = this.getParentEditor(),
            element = this.fileupload,
            fieldId='',
            widget_json = '',
            field_name = this.getValueOf( 'info', 'title'),
            field_type = 'fileupload',
            batch = this.getValueOf('info','batch'),
            isInsertMode = !element;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"batch":' + '"' + batch + '",' + '"title":' + '"' + field_name + '",' + '"class":' + '"' +field_type + '"' + '}}';
            if ( isInsertMode ) {
                element = editor.document.createElement( 'img');
                element.setAttribute( 'src', baseUrl+'/images/form/fileupload.png');
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




            // Element might be replaced by commitment.
            if ( !isInsertMode ) {
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
            element.setAttribute( 'class', field_type );
            element.setAttribute( 'title', field_name);
            element.setAttribute( 'batch', batch);
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
                labelLayout: 'horizontal',
                validate:CKEDITOR.dialog.validate.notEmpty( editor.lang.common.validateControlFailed ),
                'default': '',
                accessKey: 'N',
                onShow: function() {
                    var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                    if( element )
                        this.setValue(element.getAttribute('title'));
                },
                commit: function( element ) {
                    if ( this.getValue() )
                        element.setAttribute( 'title', this.getValue() );
                    else {
                        element.removeAttribute( 'title' );
                    }
                }
            },
            {
                type:'hbox',
                children:[
                {
                    type: 'html',
                    html: '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.tdforms.fileupload.multiple ) + '</span>'
                },
                {
                    id:'batch',
                    type: 'checkbox',
                    label: '',
                    labelLayout: 'horizontal',
                    'default': '',
                    accessKey: 'M',
                    value: "checked",
                    setup: function(element){
                        this.setValue( element.getAttribute( 'batch' ) == "true" ?  'checked' : '' );
                    }
                }
                ]
            }
            ]
        }
        ]
    };
});
