/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
控件dialog属性
*/
    var cur = new Date();
CKEDITOR.plugins.setLang( 'tdforms', 'zh-cn', {
    tdtextfield: {
        dialogTitle: '单行文本属性',
        type: '数据类型',
        minWidth: 350,
        minHeight: 150,
        charWidth: '字符宽度',
        maxChars: '最多字符数',
        width:'控件宽度（px）',
        height:'控件高度（px）',
        fontSize:'字体大小（px）',
        typeText: '文本',
        typeInt: '整型',
        typeFloat: '浮点型',
        typeDate: '日期型',
        typeEmail: '电子邮件',
        defaultCurTime: '默认当前时间'
    },
    tdtextarea: {
        dialogTitle: '多行文本属性',
        width:'控件宽度（px）',
        height:'控件高度（px）'
    },
    flowcomment: {
        dialogTitle: '回退意见区属性',
        width:'控件宽度（px）',
        height:'控件高度（px）'
    },
    tdselect: {
        dialogTitle: '菜单/列表属性',
        selectInfo: '选择信息',
        opAvail: '可选项',
        size: '高度',
        lines: '行',
        chkMulti: '允许多选',
        opText: '选项文本',
        opValue: '选项值',
        btnAdd: '添加',
        btnModify: '修改',
        btnUp: '上移',
        btnDown: '下移',
        btnSetValue: '设为初始选定',
        btnDelete: '删除'
    },
    tdcheckbox: {
        dialogTitle: '复选框属性',
        selected: '已勾选',
        position: '文本位置',
        show: '是否显示文本',
        right: '右侧',
        left: '左侧'
    },
    tdradio: {
        dialogTitle: '单选按钮属性'
    },
    tdhidden: {
        dialogTitle: '隐藏域属性'
    },
    tcount: {
        autoAddDocHeader: '是否自动套红',
        dialogTitle: '自动编号属性',
        rule: '可选规则',
        formField:'表单字段',
        code:'可选编码'
    },
    cdxdtpicker: {
        dialogTitle: '日历控件属性',
        selectInfo: '格式类型',
        date: '日期,形如:2013-01-11',
        datetime: '日期,形如:2013-01-11 11:16:33',
        time: '自定义日期格式'
    },
    suseranddept: {
        dialogTitle: '部门人员控件属性',
        selectInfo: '选择类型',
        user: '选择人员',
        dept: '选择部门',
        div: '',
        enableEdit:'是否允许修改'
    },
    listview: {
        dialogTitle: '列表控件属性',
        datasource: '数据来源',
        type:'列表类型',
        listview:'列表控件',
        countersign:'会签列表',
        serialnumber: '序号',
        headerfield: '表头',
        fieldwidth: '宽度',
        total: '合计',
        computational: '计算公式',
        fieldtype: '类型',
        defaultvalue: '值（多值之间用英文逗号隔开）',
        typeInput: '单行输入框',
        typeTextarea: '多行输入框',
        typeSelect: '下拉菜单',
        typeRadio: '单选框',
        typeCheckbox: '复选框',
        typeDate: '日期'
    },
    jsEditor: {
        dialogTitle: 'JS编辑器'
    },
    cssEditor: {
        dialogTitle: 'CSS编辑器'
    },
    signature: {
        dialogTitle: '移动签章属性',
        textarea: '验证锁定字段（用,号分割）'
    },
    countersign: {
        dialogTitle: '会签控件属性'
    },
    calculate: {
        dialogTitle: '计算控件属性',
        computational: '计算公式',
        fontSize:'字体大小（px）',
        width:'控件宽度（px）',
        height:'控件高度（px）'
    },
    fileupload: {
        dialogTitle: '附件上传属性',
        multiple: '允许批量上传'
    },
    syscode: {
        dialogTitle: '系统代码选择控件属性',
        multiple: '允许多选',
        name:'代码类型',
        width:'控件宽度（px）',
        height:'控件高度（px）',
        fontSize:'字体大小（px）',
        modify:'是否允许修改'
    },
    writesign: {
        dialogTitle: '签章控件属性',
        controlType: '控件类型',
        left: '印章位置（X）',
        top: '印章位置（Y）',
        handColor: '手写颜色',
        red:'红',
        green:'绿',
        blue:'蓝',
        black:'黑',
        white:'白',
        addSeal: '盖章',
        handWrite: '手写',
        textarea: '验证锁定字段（用,号分割）'
    },
    dimensionalcode: {
        dialogTitle: '二维码控件属性',
        textarea: '验证锁定字段（用,号分割）',
        type: '二维码类型',
        width:'控件宽度',
        height:'控件高度'        
    },
    macro:{
        dialogTitle:'宏控件属性',
        type:'宏控件类型',
        value:{
            date:'当前日期，形如：2013-11-11',
            dateZ:'当前日期，形如：2013年-11月-11日',
            dateMD:'当前日期，形如：11-11',
            year:'当前年份，形如：2013',
            yearZ:'当前年份，形如：2013年',
            month:'当前月份，形如：11',
            day:'几号，形如：23',
            week:'星期，形如：星期一',
            userId:'当前用户ID',
            userName:'当前用户名',
            deptName:'当前用户部门（长部门）',
            deptNameShort:'当前用户部门',
            deptPhoneNumber:'部门电话',
            userRole:'当前用户角色',
            userRoleOther:'当前用户辅助角色',
            formName:'表单名称',
            runName:'工作名称',
            runId:'流水号'
        }
    },
    remove: {
        dialogTitle: '删除控件'
    }
});