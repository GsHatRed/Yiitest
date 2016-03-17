/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'tdtextfield', function( editor ) {
    var acceptedTypes = {
        v_email:1,
        v_int:1,
        v_float:1,
        v_date:1,
        v_text:1
    };
    return {
        title: editor.lang.tdforms.tdtextfield.dialogTitle,
        minWidth: editor.lang.tdforms.tdtextfield.minWidth,
        minHeight: editor.lang.tdforms.tdtextfield.minHeight,
        onShow: function() {
            delete this.textField;

            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "input" && ( acceptedTypes[ element.getAttribute( 'class' ) ] || !element.getAttribute( 'type' ) ) ) {
                this.textField = element;
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor = this.getParentEditor(),
            element = this.textField,
            widget_json = '',fieldId='',
            field_name = this.getValueOf( 'info', 'title'),
            field_type = this.getValueOf( 'info', 'type'),
            fontSize = this.getValueOf('info','fontSize'),
            height = this.getValueOf('info','height'),
            width = this.getValueOf('info','width'),
            value = this.getValueOf( 'info', 'value'),
            isInsertMode = !element;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"class":' + '"' +field_type + '",'
            + '"value":' + '"' + value + '",' + '"height":' + '"' + height + '",'
            + '"width":' + '"' + width + '",' + '"fontSize":' + '"' + fontSize + '"' +'}}';

            if ( isInsertMode ) {
                element = editor.document.createElement( 'input' );
                element.setAttribute( 'type', 'text' );
            }
            $(element.$).removeAttr('style');
            element.setAttribute( 'value', value );
            element.setAttribute( 'title', field_name );
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
            var data = {
                element: element
            };

            if ( isInsertMode ) {
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
                    element.setAttribute( 'name', 'data_'+fieldId );
                    element.setAttribute( 'id', 'data_'+fieldId);
                    editor.insertElement( data.element );
                    this.commitContent( data );
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
                } else
                    editor.getSelection().selectElement( data.element );
            }
        },
        contents: [
        {
            id: 'info',
            elements: [
            {
                type: 'hbox',
                widths: [ '50%', '50%' ],
                children: [
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
                    commit: function( data ) {
                        var element = data.element;
                        if ( this.getValue() )
                            element.setAttribute( 'title', this.getValue() );
                    }
                },
                {
                    id: 'value',
                    type: 'text',
                    label: editor.lang.common.value,
                    'default': '',
                    accessKey: 'V',
                    commit: function( data ) {
                        if ( CKEDITOR.env.ie && !this.getValue() ) {
                            var element = data.element,
                            fresh = new CKEDITOR.dom.element( 'input', editor.document );
                            element.copyAttributes( fresh, {
                                value:1
                            } );
                            fresh.replace( element );
                            data.element = fresh;
                        }
                    },
                    setup: function( element ) {
                        this.setValue( element.getAttribute( 'value' ) || '' );
                    }
                }
                ]
            },
            {
                type: 'hbox',
                widths: [ '50%', '50%' ],
                children: [
                {
                    id: 'height',
                    type: 'text',
                    label: editor.lang.tdforms.tdtextfield.height,
                    'default': '',
                    accessKey: 'C',
                    validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                    setup: function( element ) {
                        this.setValue( element.$.style.height.substr(0,element.$.style.height.length - 2) );
                    }
                },
                {
                    id: 'width',
                    type: 'text',
                    label: editor.lang.tdforms.tdtextfield.width,
                    'default': '',
                    accessKey: 'M',
                    validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                    setup: function( element ) {
                        this.setValue( element.$.style.width.substr(0,element.$.style.width.length - 2) );
                    }
                }
                ],
                onLoad: function() {
                    // Repaint the style for IE7 (#6068)
                    if ( CKEDITOR.env.ie7Compat )
                        this.getElement().setStyle( 'zoom', '100%' );
                }
            },
            {
                type: 'hbox',
                widths: [ '50%', '50%' ],
                children:[
                {
                    id: 'type',
                    type: 'select',
                    label: editor.lang.tdforms.tdtextfield.type,
                    validate:CKEDITOR.dialog.validate.notEmpty( editor.lang.common.validateDataType ),
                    'default': 'v_text',
                    accessKey: 'M',
                    items: [
                    [ editor.lang.tdforms.tdtextfield.typeText,	'v_text' ],
                    [ editor.lang.tdforms.tdtextfield.typeInt,	'v_int' ],
                    [ editor.lang.tdforms.tdtextfield.typeFloat,'v_float' ],
                    [ editor.lang.tdforms.tdtextfield.typeDate,	'v_date' ],
                    [ editor.lang.tdforms.tdtextfield.typeEmail,'v_email' ]
                    ],
                    setup: function( element ) {
                        this.setValue( element.getAttribute( 'class' ) );
                    },
                    commit: function( data ) {
                        var element = data.element;
                        if ( CKEDITOR.env.ie ) {
                            var elementType = element.getAttribute( 'class' );
                            var myType = this.getValue();

                            if ( elementType != myType ) {
                                var replace = CKEDITOR.dom.element.createFromHtml( '<input type="text" class="' + myType + '"></input>', editor.document );
                                element.copyAttributes( replace, {
                                    type:1
                                } );
                                replace.replace( element );
                                data.element = replace;
                            }
                        } else
                            element.setAttribute( 'class', this.getValue() );
                    }
                },
                {
                    id:'fontSize',
                    type:'text',
                    label: editor.lang.tdforms.tdtextfield.fontSize,
                    validate: CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                    setup: function( element ) {
                        this.setValue( element.$.style.fontSize.substr(0,element.$.style.fontSize.length - 2) );
                    }
                }
                ]
            },
            ]
        }
        ]
    };
});
