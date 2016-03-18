/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
CKEDITOR.dialog.add( 'countersign', function( editor ) {
    return {
        title: editor.lang.tdforms.countersign.dialogTitle,
        minWidth: 350,
        minHeight: 220,
        onShow: function() {
            delete this.countersign;

            var element = this.getParentEditor().getSelection().getSelectedElement();
            if ( element && element.getName() == "img"  && element.getAttribute('class') == 'countersign') {
                this.countersign = element;
                this.setupContent( element );
            }
        },
        onOk: function() {
            var editor,
            countersignObj = $(window.frames["countersign"].document),
            element = this.countersign,
            fieldId='',
            widget_json = '',
            field_name = countersignObj.find('#signtitle').val(),
            signheight = countersignObj.find('#signheight').val(),
            totalheight = countersignObj.find('#totalheight').val(),
            time_type = countersignObj.find('#time_type').val(),
            handwrite = countersignObj.find('#handwrite').is(':checked'),
            addseal = countersignObj.find('#addseal').is(':checked'),
            mobilewrite = countersignObj.find('#mobilewrite').is(':checked'),
            //mobileseal = countersignObj.find('#mobileseal').is(':checked'),
            opinion = countersignObj.find('#opinion').is(':checked'),
            left = countersignObj.find('#left').val(),
            top = countersignObj.find('#top').val(),
            parase = countersignObj.find('#parase').is(':checked'),
            template = encodeURIComponent(document.getElementById("countersign").contentWindow.CKEDITOR.instances.sign.getData()),
            field_type = 'countersign',
            isInsertMode = !element;
           widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
            + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' +'"signheight":' + '"' + signheight + '",' + '"totalheight":' + '"' + totalheight + '",' + '"handwrite":' + '"' + handwrite + '",' + '"addseal":' + '"' + addseal + '",' + '"mobilewrite":' + '"' + mobilewrite + '",' + '"parase":' + '"' + parase + '",' +'"left":' + '"' + left + '",' + '"top":'+'"'+top+'",'  + '"time_type":'+ '"' + time_type + '",'+ '"class":' + '"' + field_type + '",' + '"template":' + '"' + template + '",' +'"opinion":' + '"' + opinion + '"}}';
            if ( isInsertMode ) {
                editor = this.getParentEditor();
                element = editor.document.createElement( 'img' );
                element.setAttribute( 'src', baseUrl+'/images/form/countersign.png');
                element.setAttribute( 'class', 'countersign');
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
            element.setAttribute( 'title', field_name);
            if ( isInsertMode ) {
                editor.insertElement( element );
            }
            this.commitContent( element );
        },
        contents: [
        {
            id: 'info',
            elements: [
            {
                onShow: function() {
                    var selement = editor.getSelection().getSelectedElement();
                    var eclass = selement ? selement.getAttribute( 'class' ) : '';
                    id =  selement ? selement.getAttribute( 'name' ).split('_')[1] : '';
                    var a = g_loadSignURL;
                    var b = a.replace('_randomNum',Math.floor(Math.random()*100000));
                    if(id && eclass == 'countersign')
                        b = b.replace('_id',id);
                    else
                        b = b.replace('_id','new');
                    $("#countersign").attr("src",b);
                },
                type: 'html',
                html:'<iframe style="width:740px;height:420px" id="countersign" name="countersign" src=""></iframe>'
            }
            ]
        }
        ]
    };
});
