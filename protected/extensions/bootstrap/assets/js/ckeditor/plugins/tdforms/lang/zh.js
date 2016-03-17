/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
控件dialog屬性
*/
CKEDITOR.plugins.setLang( 'tdforms', 'zh', {
    tdtextfield: {
        dialogTitle: '單行文本屬性',
        type: '數據類型',
        minWidth: 350,
        minHeight: 150,
        charWidth: '字符寬度',
        maxChars: '最多字符數',
        fontSize:'字體大小（px）',
        typeText: '文本',
        typeInt: '整型',
        typeFloat: '浮點型',
        typeDate: '日期型',
        typeEmail: '電子郵件'
    },
    tdtextarea: {
        dialogTitle: '多行文本屬性',
        cols: '字符寬度',
        rows: '行數'
    },
    tdselect: {
        dialogTitle: '菜單/列表屬性',
        selectInfo: '選擇信息',
        opAva​​il: '可選項',
        size: '高度',
        lines: '行',
        chkMulti: '允許多選',
        opText: '選項文本',
        opValue: '選項值',
        btnAdd: '添加',
        btnModify: '修改',
        btnUp: '上移',
        btnDown: '下移',
        btnSetValue: '設為初始選定',
        btnDelete: '刪除'
    },
    tdcheckbox: {
        dialogTitle: '複選框屬性',
        selected: '已勾選'
    },
    tdradio: {
        dialogTitle: '單選按鈕屬性'
    },
    tdhidden: {
        dialogTitle: '隱藏域屬性'
    },
    sinput: {
        dialogTitle: '特殊文本屬性',
        selectInfo: '選項'
    },
    cdxdtpicker: {
        dialogTitle: '日曆控件屬性',
        selectInfo: '格式類型',
        date: '日期,形如:2013-01-11',
        datetime: '日期,形如:2013-01-11 11:16:33',
        time: '自定義日期格式'
    },
    suseranddept: {
        dialogTitle: '部門人員控件屬性',
        selectInfo: '選擇類型',
        user: '選擇人員',
        dept: '選擇部門',
        div: ''
    },
    listview: {
        dialogTitle: '列表控件屬性',
        datasource: '數據來源',
        type:'列表類型',
        listview:'列表控件',
        countersign:'會簽列表',
        serialnumber: '序號',
        headerfield: '表頭',
        fieldwidth: '寬度',
        total: '合計',
        computational: '計算公式',
        fieldtype: '類型',
        defaultvalue: '值（多值之間用英文逗號隔開）',
        typeInput: '單行輸入框',
        typeTextarea: '多行輸入框',
        typeSelect: '下拉菜單',
        typeRadio: '單選框',
        typeCheckbox: '複選框',
        typeDate: '日期'
    },
    jsEditor: {
        dialogTitle: 'JS編輯器'
    },
    cssEditor: {
        dialogTitle: 'CSS編輯器'
    },
    signature: {
        dialogTitle: '移動簽章屬性',
        textarea: '驗證鎖定字段（用,號分割）'
    },
    writesign: {
        dialogTitle: '簽章控件屬性',
        controlType: '控件類型',
        handColor: '手寫顏色',
        red:'紅',
        green:'綠',
        blue:'藍',
        black:'黑',
        white:'白',
        addSeal: '蓋章',
        handWrite: '手寫',
        textarea: '驗證鎖定字段（用,號分割）'
    },
    dimensionalcode: {
        dialogTitle: '二維碼控件屬性',
        textarea: '驗證鎖定字段（用,號分割）',
        type: '二維碼類型'
    },
 macro:{
        dialogTitle:'宏控件屬性',
        type:'宏控件類型',
        value:{
            date:'當前日期，形如：2013-11-11',
            dateZ:'當前日期，形如：2013年-11月-11日',
            year:'當前年份，形如：2013',
            yearZ:'當前年份，形如：2013年',
            month:'當前月份，形如：11',
            userId:'當前用戶ID',
            userName:'当前用户名',
            deptName:'当前用户部门（长部门）',
            deptNameShort:'当前用户部门',
            userRole:'当前用户角色',
            userRoleOther:'当前用户辅助角色',
            formName:'表单名称',
            runName:'工作名称',
            runId:'流水号'
        }
    },
    remove: {
        dialogTitle: '刪除控件'
    }
});