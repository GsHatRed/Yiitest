CKEDITOR.dialog.add('syscode', function(editor) {

    function getOptions(combo) {
        combo = getSelect(combo);
        return combo ? combo.getChildren() : false;
    }

    function getSelect(obj) {
        if (obj && obj.domId && obj.getInputElement().$) // Dialog element.
            return obj.getInputElement();
        else if (obj && obj.$)
            return obj;
        return false;
    }
var items = $.ajax({
                type: 'GET',
                url: g_loadCodeURL,
                async: false,
                dataType: 'json'
            }).responseText;    
    return {
        title: editor.lang.tdforms.syscode.dialogTitle,
        minWidth: 400,
        minHeight: 240,
        onLoad: function() {

        },
        onShow: function() {
            var selection = editor.getSelection(),
                    element = selection.getStartElement();
            if (element)
                element = element.getAscendant('img', true);

            if (!element || element.getName() != 'img' || element.data('cke-realelement')) {
                element = editor.document.createElement('img');
                this.insertMode = true;
            }
            else
                this.insertMode = false;
            this.element = element;
            if (!this.insertMode)
                this.setupContent(this.element);
        },
        contents: [
            {
                id: '_form',
                elements: [
                    {
                        type: 'text',
                        id: 'title',
                        label: editor.lang.common.controlName,
                        labelLayout: 'horizontal',
                        validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.common.validateControlFailed),
                        setup: function(element) {
                            this.setValue(element.getAttribute("title"));
                        },
                        commit: function(element) {
                            element.setAttribute("title", this.getValue());
                        }
                    },
                    {
                        type: 'select',
                        id: 'code_type',
                        label: editor.lang.tdforms.syscode.name,
                        labelLayout: 'horizontal',
                        items: eval(items),
                        setup: function(element) {
                            this.setValue(element.getAttribute('value'));
                        },
                        commit: function(element) {
                            var dialog = this.getDialog(),
                                    optionsNames = getOptions(this),
                                    optionsValues = getOptions(dialog.getContentElement('_form', 'code_type')),
                                    selectValue = dialog.getContentElement('_form', 'code_type').getValue();
                            element.setAttribute('class', 'syscode');
                            for (var i = 0; i < optionsNames.count(); i++) {
                                if (optionsValues.getItem(i).getValue() == selectValue) {
                                    element.setAttribute('value', optionsValues.getItem(i).getValue());
                                }
                            }
                        }
                    },
                    {
                        type: 'text',
                        id: 'wheight',
                        labelLayout: 'horizontal',
                        label: editor.lang.widget.wheight,
                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                        setup: function(element) {
                            this.setValue(element.$.style.height.substr(0, element.$.style.height.length - 2));
                        }
                    },
                    {
                        type: 'text',
                        labelLayout: 'horizontal',
                        id: 'wwidth',
                        label: editor.lang.widget.wwidth,
                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                        setup: function(element) {
                            this.setValue(element.$.style.width.substr(0, element.$.style.width.length - 2));
                        }
                    },
                    {
                        type: 'text',
                        labelLayout: 'horizontal',
                        id: 'fontSize',
                        label: editor.lang.widget.fontSize,
                        validate: CKEDITOR.dialog.validate.integer(editor.lang.common.validateNumberFailed),
                        setup: function(element) {
                            this.setValue(element.$.style.fontSize.substr(0, element.$.style.fontSize.length - 2));
                        }
                    },
                    {
                        type: 'hbox',
                        children: [
                            {
                                type: 'html',
                                html: '<span>' + CKEDITOR.tools.htmlEncode(editor.lang.tdforms.suseranddept.enableEdit) + '</span>'
                            },
                            {
                                id: 'modify',
                                type: 'checkbox',
                                label: '',
                                labelLayout: 'horizontal',
                                'default': '',
                                accessKey: 'M',
                                value: "checked",
                                setup: function(element) {
                                    this.setValue(element.getAttribute('modify') == "true" ? 'checked' : '');
                                }
                            }
                        ]
                    }
                ]
            }
        ],
        onOk: function() {
            var dialog = this, widget_json = '', fieldId = '',
                    field_name = this.getValueOf('_form', 'title'),
                    field_type = 'syscode',
                    height = this.getValueOf('_form', 'wheight'),
                    width = this.getValueOf('_form', 'wwidth'),
                    fontSize = this.getValueOf('_form', 'fontSize'),
                    modify = this.getValueOf('_form', 'modify'),
                    syscode = this.element;
            widget_json += '{' + '"field_name":' + '"' + field_name + '",' + '"field_type":' + '"' + field_type + '",'
                    + '"field_attr":' + '{' + '"title":' + '"' + field_name + '",' + '"modify":' + '"' + modify + '",' + '"class":' + '"' + field_type + '",'
                    + '"value":' + '"' + this.getValueOf('_form', 'code_type') + '",' + '"height":' + '"' + height + '",' + '"width":' + '"' + width + '",' + '"fontSize":' + '"' + fontSize + '"' + '}}';

            if (this.insertMode) {
                fieldId = $.ajax({
                    type: "post",
                    url: g_requestURL,
                    data: {
                        fieldInfo: widget_json,
                        formID: g_form_id
                    },
                    cache: false,
                    async: false
                }).responseText;
                if (fieldId == 'repeat') {
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                } else {
                    syscode.setAttribute('src', baseUrl + '/images/form/syscode.png');
                    syscode.setAttribute('name', "data_" + fieldId);
                    syscode.setAttribute('id', 'data_' + fieldId);
                    editor.insertElement(syscode);
                }
            } else {
                fieldId = syscode.getAttribute('name').split('_')[1];
                fieldId = $.ajax({
                    type: "post",
                    url: g_requestURL,
                    data: {
                        fieldInfo: widget_json,
                        formID: g_form_id,
                        fieldId: fieldId
                    },
                    cache: false,
                    async: false
                }).responseText;
                if (fieldId == 'repeat') {
                    _originalAlert(field_name + ' 字段已存在！');
                    return false;
                }
            }
            syscode.setAttribute('modify', modify);
            syscode.setAttribute('title', dialog.getValueOf('_form', 'title'));
            $(syscode.$).removeAttr('style');
            if (fontSize)
                $(syscode.$).css({
                    "font-size": fontSize + "px"
                });
            if (height)
                $(syscode.$).css({
                    "height": height + "px"
                });
            if (width)
                $(syscode.$).css({
                    "width": width + "px"
                });
            this.commitContent(syscode);
        }
    };
});