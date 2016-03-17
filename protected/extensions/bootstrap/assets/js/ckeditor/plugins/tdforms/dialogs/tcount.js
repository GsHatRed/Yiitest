/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'tcount', function( editor ) {
    // Add a new option to a SELECT object (combo or list).
    function addOption( combo, optionText, optionValue, documentObject, index ) {
        combo = getSelect( combo );
        var oOption;
        if ( documentObject )
            oOption = documentObject.createElement( "OPTION" );
        else
            oOption = document.createElement( "OPTION" );

        if ( combo && oOption && oOption.getName() == 'option' ) {
            if ( CKEDITOR.env.ie ) {
                if ( !isNaN( parseInt( index, 10 ) ) )
                    combo.$.options.add( oOption.$, index );
                else
                    combo.$.options.add( oOption.$ );

                oOption.$.innerHTML = optionText.length > 0 ? optionText : '';
                oOption.$.value = optionValue;
            } else {
                if ( index !== null && index < combo.getChildCount() )
                    combo.getChild( index < 0 ? 0 : index ).insertBeforeMe( oOption );
                else
                    combo.append( oOption );

                oOption.setText( optionText.length > 0 ? optionText : '' );
                oOption.setValue( optionValue );
            }
        } else
            return false;

        return oOption;
    }
    // Remove all selected options from a SELECT object.
    function removeSelectedOptions( combo ) {
        combo = getSelect( combo );

        // Save the selected index
        var iSelectedIndex = getSelectedIndex( combo );

        // Remove all selected options.
        for ( var i = combo.getChildren().count() - 1; i >= 0; i-- ) {
            if ( combo.getChild( i ).$.selected )
                combo.getChild( i ).remove();
        }

        // Reset the selection based on the original selected index.
        setSelectedIndex( combo, iSelectedIndex );
    }
    //Modify option  from a SELECT object.
    function modifyOption( combo, index, title, value ) {
        combo = getSelect( combo );
        if ( index < 0 )
            return false;
        var child = combo.getChild( index );
        child.setText( title );
        child.setValue( value );
        return child;
    }

    function removeAllOptions( combo ) {
        combo = getSelect( combo );
        while ( combo.getChild( 0 ) && combo.getChild( 0 ).remove() ) {
        /*jsl:pass*/
        }
    }
    // Moves the selected option by a number of steps (also negative).
    function changeOptionPosition( combo, steps, documentObject ) {
        combo = getSelect( combo );
        var iActualIndex = getSelectedIndex( combo );
        if ( iActualIndex < 0 )
            return false;

        var iFinalIndex = iActualIndex + steps;
        iFinalIndex = ( iFinalIndex < 0 ) ? 0 : iFinalIndex;
        iFinalIndex = ( iFinalIndex >= combo.getChildCount() ) ? combo.getChildCount() - 1 : iFinalIndex;

        if ( iActualIndex == iFinalIndex )
            return false;

        var oOption = combo.getChild( iActualIndex ),
        sText = oOption.getText(),
        sValue = oOption.getValue();

        oOption.remove();

        oOption = addOption( combo, sText, sValue, ( !documentObject ) ? null : documentObject, iFinalIndex );
        setSelectedIndex( combo, iFinalIndex );
        return oOption;
    }

    function getSelectedIndex( combo ) {
        combo = getSelect( combo );
        return combo ? combo.$.selectedIndex : -1;
    }

    function setSelectedIndex( combo, index ) {
        combo = getSelect( combo );
        if ( index < 0 )
            return null;
        var count = combo.getChildren().count();
        combo.$.selectedIndex = ( index >= count ) ? ( count - 1 ) : index;
        return combo;
    }

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
    //加载外部数据

    var items = $.ajax({
        type:'GET',
        url:g_loadRuleURL,
        async:false,
        dataType: 'json'
    }).responseText;
    var formFields = $.ajax({
        type:'GET',
        url:g_loadFieldURL,
        async:false,
        dataType: 'json'
    }).responseText;
    return {
        title: editor.lang.tdforms.tcount.dialogTitle,
        minWidth: CKEDITOR.env.ie ? 460 : 395,
        minHeight: CKEDITOR.env.ie ? 320 : 300,
        onShow: function() {
            delete this.tcount;
            this.setupContent( 'clear' );
            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "img" && element.getAttribute('class') == 'tcount' ) {
                this.tcount = element;
                this.setupContent( element.getName(), element );

                // Load Options into dialog.
                var objOptions = getOptions( element );
                for ( var i = 0; i < objOptions.count(); i++ )
                    this.setupContent( 'option', objOptions.getItem( i ) );
            }
        },
        onOk: function() {
            var editor = this.getParentEditor(),
            element = this.tcount,
            fieldId='',
            widget_json = '',
            field_name = this.getValueOf( 'info', 'title'),
            rule_id = this.getValueOf('info', 'ruleID'),
            autoheader = this.getValueOf('info','autoheader'),
            field_type = 'tcount',
            isInsertMode = !element;
            function getRelations(obj){
                var dialog = obj,
                fieldNameObj = dialog.getContentElement('info','fieldName'),
                ruleNameObj = dialog.getContentElement('info','ruleName');
                var dataIds = [],rules = [];
                if($('#'+fieldNameObj.domId +' select option').length > 0) {
                    var bind_data = '{';
                    $('#'+fieldNameObj.domId +' select option').each(function(){
                        dataIds.push($(this).val());
                    });
                    $('#'+ruleNameObj.domId +' select option').each(function(){
                        rules.push($(this).val());
                    });
                    for(var i=0;i<dataIds.length;i++){
                        bind_data += '"'+dataIds[i]+'":' + '"' + rules[i] + '",';
                    }
                    bind_data = bind_data.substr(bind_data.length-1) == ',' ? bind_data.substr(0,bind_data.length-1) : bind_data;
                    bind_data += '}';
                    $('#'+fieldNameObj.domId).find('select').html('');
                    $('#'+ruleNameObj.domId).find('select').html('');
                    return bind_data;
                }
                return false;
            }
            var relations = getRelations(this);
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"autoheader":' + '"' + autoheader + '",' + '"class":' + '"' +field_type + '",'
            + '"value":' + '"' + rule_id + '",';
            if(relations)
                widget_json +='"relations":' + relations;
            widget_json += '}}';
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
                    editor = this.getParentEditor();
                    element = editor.document.createElement( 'img' );
                    element.setAttribute( 'src', baseUrl+'/images/form/tcount.png');
                    element.setAttribute( 'name', "data_"+fieldId );
                    element.setAttribute( 'id', 'data_'+fieldId);
                }

            }
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
            element.setAttribute( 'class', 'tcount' );
            element.setAttribute( 'title', field_name);
            element.setAttribute('rule_id',rule_id);
            element.setAttribute('relations', relations);
            element.setAttribute( 'autoheader', autoheader);
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
                type: 'hbox',
                widths: [ '50%','50%' ],
                children:[
                {
                    id: 'title',
                    type: 'text',
                    widths: [ '50%', '50%' ],
                    labelLayout: 'horizontal',
                    label: editor.lang.common.controlName,
                    validate:CKEDITOR.dialog.validate.notEmpty( editor.lang.common.validateControlFailed ),
                    'default': '',
                    setup: function() {
                        var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                        if(element)
                            this.setValue( element.getAttribute( 'title' ) || '' );
                    },
                    onShow:function(){
                        var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                        if(element)
                            this.setValue( element.getAttribute( 'title' ) || '' );
                    },
                    commit: function( element ) {
                        if ( this.getValue() )
                            element.data( 'cke-saved-title', this.getValue());
                        else {
                            element.data( 'cke-saved-title', false );
                            element.removeAttribute( 'title' );
                        }
                    }
                },
                {
                    type:'select',
                    id: 'ruleID',
                    label: editor.lang.tdforms.tcount.rule,
                    validate:CKEDITOR.dialog.validate.notEmpty('请选择规则！'),
                    labelLayout: 'horizontal',
                    items: eval(items),
                    widths: [ '50%', '50%' ],
                    'default': '',
                    onLoad: function() {
                    //                    this.getInputElement().setAttribute( 'readOnly', true );
                    },
                    onChange:function(){
                        var dialog = this.getDialog(),
                        ruleID = dialog.getValueOf( 'info', 'ruleID'),
                        fieldNameObj = dialog.getContentElement('info','fieldName'),
                        ruleNameObj = dialog.getContentElement('info','ruleName');
                        var fillSelectObj = dialog.getContentElement( 'info', 'selectRule');
                        if(ruleID) {
                            var a = g_loadExpressionURL;
                            var b = a.replace('_ruleID',ruleID);
                            var expression = $.ajax({
                                type:'GET',
                                url:b,
                                async:false,
                                dataType: 'json'
                            }).responseText;
                            $('#'+fillSelectObj.domId).find('select').html(expression);
                            $('#'+fieldNameObj.domId).find('select').html('');
                            $('#'+ruleNameObj.domId).find('select').html('');
                        }
                    },
                    setup: function() {
                        var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                        if( element )
                            this.setValue(element.getAttribute('rule_id'));
                    }
                }
                ]
            },
            {
                type:'hbox',
                children:[
                {
                type: 'html',
                    html: '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.tdforms.tcount.autoAddDocHeader) + '</span>'
                },
                {
                    id:'autoheader',
                    type: 'checkbox',
                    label: '',
                    labelLayout: 'horizontal',
                    'default': '',
                    accessKey: 'M',
                    value: "checked",
                    setup: function(){
                        var element = this.getDialog().getParentEditor().getSelection().getSelectedElement();
                        if(element.getAttribute( 'autoheader' )) {
                            this.setValue( element.getAttribute( 'autoheader' ) == "true" ?  'checked' : '' );
                        }
                    }
                }
                ]
            },            
            {
                type: 'html',
                html: '<span>' + CKEDITOR.tools.htmlEncode( editor.lang.tdforms.tdselect.opAvail ) + '</span>'
            },
            {
                type: 'hbox',
                widths: [ '115px', '115px', '100px' ],
                children: [
                {
                    type: 'vbox',
                    children: [
                    {
                        id: 'selectField',
                        type: 'select',
                        label: editor.lang.tdforms.tcount.formField,
                        items:eval(formFields),
                        style: 'width:115px',
                        setup: function( name, element ) {
                            if ( name == 'clear' )
                                this.setValue( "" );
                        }
                    },
                    {
                        type: 'select',
                        id: 'fieldName',
                        label: '',
                        title: '',
                        size: 5,
                        style: 'width:115px;height:75px',
                        items: [],
                        onChange: function() {
                            var dialog = this.getDialog(),
                            values = dialog.getContentElement( 'info', 'ruleName' ),
                            optField = dialog.getContentElement( 'info', 'selectField' ),
                            optRule = dialog.getContentElement( 'info', 'selectRule' ),
                            iIndex = getSelectedIndex( this );

                            setSelectedIndex( values, iIndex );
                            optField.setValue( this.getValue() );
                            optRule.setValue( values.getValue() );
                        },
                        onShow:function(){
                            var dialog = this.getDialog(),
                            optField = dialog.getContentElement( 'info', 'selectField' ),
                            names = dialog.getContentElement( 'info', 'fieldName' );
                            var element = dialog.getParentEditor().getSelection().getSelectedElement();
                            if(element) {
                                var relations = element.getAttribute( 'relations' );
                                if(relations){
                                    $.each($.parseJSON(relations), function(key , value){
                                        var fieldName = $('#'+optField.domId + ' select').find('option[value="'+key+'"]').text();
                                        addOption( names, fieldName, key, dialog.getParentEditor().document );
                                    });
                                }
                            }
                        },
                        setup: function() {
                        }
                    }
                    ]
                },
                {
                    type: 'vbox',
                    children: [
                    {
                        id: 'selectRule',
                        type: 'select',
                        label: editor.lang.tdforms.tcount.code,
                        items:[],
                        style: 'width:115px',
                        setup: function( name, element ) {
                            if ( name == 'clear' )
                                this.setValue( "" );
                        }
                    },
                    {
                        type: 'select',
                        id: 'ruleName',
                        label: '',
                        size: 5,
                        style: 'width:115px;height:75px',
                        items: [],
                        onChange: function() {
                            var dialog = this.getDialog(),
                            names = dialog.getContentElement( 'info', 'fieldName' ),
                            optField = dialog.getContentElement( 'info', 'selectField' ),
                            optRule = dialog.getContentElement( 'info', 'selectRule' ),
                            iIndex = getSelectedIndex( this );

                            setSelectedIndex( names, iIndex );
                            optField.setValue( names.getValue() );
                            optRule.setValue( this.getValue() );
                        },
                        onShow:function(){
                            var dialog = this.getDialog(),
                            optRule = dialog.getContentElement( 'info', 'selectRule' ),
                            values = dialog.getContentElement( 'info', 'ruleName' );
                            var element = dialog.getParentEditor().getSelection().getSelectedElement();
                            if(element){
                                var relations = element.getAttribute( 'relations' );
                                if(relations){
                                    $.each($.parseJSON(relations), function(key , value){
                                        var ruleName = $('#'+optRule.domId + ' select').find('option[value="'+value+'"]').text();
                                        addOption( values, ruleName, value, dialog.getParentEditor().document );
                                    });
                                }
                            }
                        },
                        setup: function( name, element ) {}
                    }
                    ]
                },
                {
                    type: 'vbox',
                    padding: 20,
                    children: [
                    {
                        type: 'button',
                        id: 'btnAdd',
                        style: '',
                        label: editor.lang.tdforms.tdselect.btnAdd,
                        title: editor.lang.tdforms.tdselect.btnAdd,
                        style: 'width:100%;',
                        onClick: function() {
                            var dialog = this.getDialog(),
                            optField = dialog.getContentElement( 'info', 'selectField' ),
                            optRule = dialog.getContentElement( 'info', 'selectRule' ),
                            names = dialog.getContentElement( 'info', 'fieldName' ),
                            values = dialog.getContentElement( 'info', 'ruleName' );
                            var fieldName = $('#'+optField.domId + ' select').find('option:selected').text();
                            var ruleName = $('#'+optRule.domId + ' select').find('option:selected').text();
                            if(!optRule.getValue() || !optField.getValue()) {
                                _originalAlert('选项值不能为空！');
                                return false;
                            } else {
                                if($('#'+names.domId + ' select option[value="'+optField.getValue()+'"]').length > 0) {
                                    _originalAlert('字段 '+ '"'+fieldName+'"'+' 已添加映射！');
                                    return false;
                                }
                            }
                            addOption( names, fieldName, optField.getValue(), dialog.getParentEditor().document );
                            addOption( values, ruleName, optRule.getValue(), dialog.getParentEditor().document );
                            optField.setValue( "" );
                            optRule.setValue( "" );
                        }
                    },
                    {
                        type: 'button',
                        id: 'btnDelete',
                        label: editor.lang.tdforms.tdselect.btnDelete,
                        title: editor.lang.tdforms.tdselect.btnDelete,
                        style: 'width:100%;',
                        onClick: function() {
                            var dialog = this.getDialog(),
                            optField = dialog.getContentElement( 'info', 'selectField' ),
                            optRule = dialog.getContentElement( 'info', 'selectRule' ),
                            names = dialog.getContentElement( 'info', 'fieldName' ),
                            values = dialog.getContentElement( 'info', 'ruleName' );
                            var  iIndex = getSelectedIndex( names );
                            if( iIndex >= 0 ) {
                                removeSelectedOptions( names );
                                removeSelectedOptions( values );

                                optField.setValue( "" );
                                optRule.setValue( "" );
                            }
                        }
                    }
                    ]
                }
                ]
            }
            ]
        }
        ]
    };
});
