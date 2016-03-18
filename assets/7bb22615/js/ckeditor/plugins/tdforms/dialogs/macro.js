CKEDITOR.dialog.add( 'macro', function( editor ) {

    function getOptions( combo ) {
        combo = getSelect( combo );
        return combo ? combo.getChildren() : false;
    }

    function getSelect( obj ) {
        if ( obj && obj.domId && obj.getInputElement().$ ) // Dialog element.
            return obj.getInputElement();
        else if ( obj && obj.$ )
            return obj;
        return false;
    }
    return {
        title : editor.lang.tdforms.macro.dialogTitle,
        minWidth : 400,
        minHeight : 240,
        onLoad: function() {

        },
        onShow: function() {
            var selection = editor.getSelection(),
            element = selection.getStartElement();
            if ( element )
                element = element.getAscendant( 'img', true );

            if ( !element || element.getName() != 'img' || element.data( 'cke-realelement' ) ) {
                element = editor.document.createElement( 'img' );
                this.insertMode = true;
            }
            else
                this.insertMode = false;
            this.element = element;
            if ( !this.insertMode )
                this.setupContent( this.element );
        },
        contents: [
        {
            id: '_form',
            elements: [
            {
                type:'text',
                id:'title',
                label:editor.lang.common.controlName,
                labelLayout:'horizontal',
                validate:CKEDITOR.dialog.validate.notEmpty( editor.lang.common.validateControlFailed ),
                setup: function( element ) {
                    this.setValue( element.getAttribute( "title" ) );
                },
                commit: function( element ) {
                    element.setAttribute( "title", this.getValue() );
                }
            },
            {
                type:'select',
                id: 'macro_type',
                label: editor.lang.tdforms.macro.type,
                'default':'date',
                labelLayout: 'horizontal',
                items: [
                [ editor.lang.tdforms.macro.value.date,	'date' ],
                [ editor.lang.tdforms.macro.value.dateZ,	'dateZ' ],
                [ editor.lang.tdforms.macro.value.dateMD, 'dateMD'],
                [ editor.lang.tdforms.macro.value.year,	'year' ],
                [ editor.lang.tdforms.macro.value.yearZ,	'yearZ' ],
                [ editor.lang.tdforms.macro.value.month,	'month' ],
                [ editor.lang.tdforms.macro.value.day,	'day' ],
                [ editor.lang.tdforms.macro.value.week,	'week' ],
                [ editor.lang.tdforms.macro.value.userId,	'userId' ],
                [ editor.lang.tdforms.macro.value.userName,	'userName' ],
                [ editor.lang.tdforms.macro.value.deptName,	'deptName' ],
                [ editor.lang.tdforms.macro.value.deptNameShort,	'deptNameShort' ],
                [ editor.lang.tdforms.macro.value.deptPhoneNumber,	'deptPhoneNumber' ]
//                [ editor.lang.tdforms.macro.value.userRole,	'userRole' ]
//                [ editor.lang.tdforms.macro.value.userRoleOther,	'userRoleOther' ],
//                [ editor.lang.tdforms.macro.value.formName,	'formName' ],
//                [ editor.lang.tdforms.macro.value.runName,	'runName' ],
//                [ editor.lang.tdforms.macro.value.runId,	'runId' ]
                ],
                setup: function( element ) {
                    this.setValue( element.getAttribute('value') );
                },
                commit: function( element ) {
                    var dialog = this.getDialog(),
                    optionsNames = getOptions( this ),
                    optionsValues = getOptions( dialog.getContentElement( '_form', 'macro_type' ) ),
                    selectValue = dialog.getContentElement( '_form', 'macro_type' ).getValue();
                    element.setAttribute( 'class', 'macro' );
                    for ( var i = 0; i < optionsNames.count(); i++ ) {
                        if ( optionsValues.getItem( i ).getValue() == selectValue ) {
                            element.setAttribute( 'value', optionsValues.getItem( i ).getValue() );
                        }
                    }
                }
            },
            {
                type: 'text',
                id: 'wheight',
                labelLayout: 'horizontal',
                label: editor.lang.widget.wheight,
                validate:CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                setup: function( element ) {
                    this.setValue( element.$.style.height.substr(0,element.$.style.height.length - 2) );
                }
            },
            {
                type: 'text',
                labelLayout: 'horizontal',
                id: 'wwidth',
                label: editor.lang.widget.wwidth,
                validate:CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                setup: function( element ) {
                    this.setValue( element.$.style.width.substr(0,element.$.style.width.length - 2) );
                }
            },
            {
                type: 'text',
                labelLayout: 'horizontal',
                id: 'fontSize',
                label: editor.lang.widget.fontSize,
                validate:CKEDITOR.dialog.validate.integer( editor.lang.common.validateNumberFailed ),
                setup: function( element ) {
                    this.setValue( element.$.style.fontSize.substr(0,element.$.style.fontSize.length - 2) );
                }
            },
            {
                type:'hbox',
                children:[
                {
                    type: 'html',
                    html: '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.tdforms.suseranddept.enableEdit ) + '</span>'
                },
                {
                    id:'modify',
                    type: 'checkbox',
                    label: '',
                    labelLayout: 'horizontal',
                    'default': '',
                    accessKey: 'M',
                    value: "checked",
                    setup: function(element){
                        this.setValue( element.getAttribute( 'modify' ) == "true" ?  'checked' : '' );
            }
                }
            ]
        }
            ]
        }
        ],
        onOk: function() {
            var dialog = this,widget_json = '',fieldId='',
            field_name = this.getValueOf( '_form', 'title'),
            field_type = 'macro',
            height = this.getValueOf('_form','wheight'),
            width = this.getValueOf('_form','wwidth'),
            fontSize = this.getValueOf('_form','fontSize'),
            modify = this.getValueOf('_form','modify'),
            macro = this.element;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"modify":' + '"' + modify + '",' + '"class":' + '"' +field_type + '",'
            + '"value":' + '"' + this.getValueOf('_form','macro_type') + '",' + '"height":' + '"' + height + '",' + '"width":' + '"' + width + '",' + '"fontSize":' + '"' + fontSize + '"' + '}}';

            if ( this.insertMode ) {
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
                } else{
                macro.setAttribute( 'src',baseUrl+'/images/form/macro.png');
                    macro.setAttribute( 'name', "data_"+fieldId );
                    macro.setAttribute( 'id', 'data_'+fieldId);
                editor.insertElement( macro );
                }
            } else {
                fieldId = macro.getAttribute( 'name' ).split('_')[1];
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
            macro.setAttribute( 'modify', modify);
            macro.setAttribute( 'title', dialog.getValueOf( '_form', 'title' ) );
            $(macro.$).css({
                "width":width+"px",
                "height":height+"px",
                "font-size":fontSize+"px"
                });
            this.commitContent( macro );
        }
    };
} );