/**
 *  ntkoObj.docOpen = TANGER_OCX_bDocOpen
 *  ntkoObj.op = TANGER_OCX_strOp
 *  ntkoObj.fileName = TANGER_OCX_filename
 *  ntkoObj.attachName = TANGER_OCX_attachName
 *  ntkoObj.attachURL = TANGER_OCX_attachURL
 *  ntkoObj.actionURL = TANGER_OCX_actionURL
 *  ntkoObj.obj = TANGER_OCX_OBJ
 *  ntkoObj.user = TANGER_OCX_user
 */
var NTKO = function(option) {
    this.obj = {};
    this.docOpen = false;
    var def = {
        id: "0",
        formUrl:'',
        prefix: "NTKO_",
        width: "100%",
        height: "100%",
        op:"",
        fileName:"",
        attachName:"",
        attachURL:"##",
        templateURL:"",
        actionURL:"##",
        ntkopdfdocUrl:'##',
        aipURL:'',
        debug: false,
        user: 'admin',
        showRevisions: false,
        trackRevisions:false,
        silentMode: false
    };
    var p = extend(def, option, true);

    this.id = p.id;
    this.formUrl = p.formUrl;
    this.prefix = p.prefix;
    this.width = p.width;
    this.height = p.height;
    this.op = p.op;
    this.fileName = p.fileName;
    this.attachName = p.attachName;
    this.attachURL = p.attachURL;
    this.templateURL = p.templateURL;
    this.actionURL = p.actionURL;
    this.ntkopdfdocUrl = p.ntkopdfdocUrl;
    this.aipURL = p.aipURL;
    this.debug = p.debug;
    this.user = p.user;
    this.showRevisions = p.showRevisions;
    this.trackRevisions = p.trackRevisions;
    this.silentMode = p.silentMode;
    this.obj = document.getElementById('TANGER_OCX');
}
NTKO.showDialog = function(num){
    ntkoObj.obj.Close();
    ntkoObj.obj.ShowDialog(num);
}
//以下为V1.7新增函数示例

/**
 *  从本地增加图片到文档指定位置
 *
 */
NTKO.addPicFromLocal = function () {
    if(ntkoObj.docOpen) {
        ntkoObj.obj.AddPicFromLocal(
            "", //路径
            true,//是否提示选择文件
            true,//是否浮动图片
            100,//如果是浮动图片，相对于左边的Left 单位磅
            100,//如果是浮动图片，相对于当前段落Top
            1, //当前光标处
            100,//无缩放
            1 //文字上方
            );
    }
}

/**
 *  从URL增加图片到文档指定位置
 *
 */
NTKO.addPicFromURL = function (URL) {
    if(ntkoObj.docOpen) {
        ntkoObj.obj.AddPicFromURL(
            URL,//URL 注意；URL必须返回Word支持的图片类型。
            true,//是否浮动图片
            150,//如果是浮动图片，相对于左边的Left 单位磅
            150//如果是浮动图片，相对于当前段落Top
            );
    }
}
NTKO.addPDF417Code = function(){
    if(ntkoObj.docOpen) {
        ntkoObj.obj.AddPicFromURL(
            dimensionalUrl,//URL 注意；URL必须返回Word支持的图片类型。
            true,//是否浮动图片
            150,//如果是浮动图片，相对于左边的Left 单位磅
            150//如果是浮动图片，相对于当前段落Top
            );
    }    
}

//从本地增加印章文档指定位置
NTKO.addSignFromLocal = function (key)
{
    if(ntkoObj.docOpen)
    {
        if(ntkoObj.obj.IsNTKOSecSignInstalled())
            ntkoObj.obj.AddSecSignFromLocal(ntkoObj.user, "", true, 0, 0, 1);
        else
            ntkoObj.obj.AddSignFromLocal(ntkoObj.user, "", true, 0, 0, key);
    }
}

//从URL增加印章文档指定位置
NTKO.addSignFromURL = function (key)
{
    if(document.all("ATTACH_DIR").value=="" && document.all("DISK_ID").value=="")
    {
        _originalAlert("请从文件柜或网络硬盘选择印章!");
        return;
    }
    var URL,ym,attachment_id,attach_dir;
    attach_dir=document.all("ATTACH_DIR").value;
    if(document.all("DISK_ID").value=="")
    {
        ym=attach_dir.substr(0,attach_dir.indexOf("_"));
        attachment_id=attach_dir.substr(attach_dir.indexOf("_")+1);
    }

    if(document.all("DISK_ID").value=="")
        URL = "/inc/attach.php?MODULE=file_folder&YM="+ym+"&ATTACHMENT_ID="+attachment_id+"&ATTACHMENT_NAME="+document.all("ATTACH_NAME").value;
    else
        URL = "/inc/netdisk.php?DISK_ID="+document.all("DISK_ID").value+"&FILE_NAME="+document.all("ATTACH_DIR").value+"/"+document.all("ATTACH_NAME").value;
    if(ntkoObj.docOpen)
    {
        if(ntkoObj.obj.IsNTKOSecSignInstalled())
            ntkoObj.obj.AddSecSignFromURL(ntkoObj.user, URL, 50, 50, 1);
        else
            ntkoObj.obj.AddSignFromURL(ntkoObj.user, URL, 50, 50, key);
    }
}

NTKO.addSecSignFromEkey = function ()
{
    if(ntkoObj.obj.IsNTKOSecSignInstalled())
        ntkoObj.obj.AddSecSignFromEkey(ntkoObj.user, 0, 0, 1);
}
//开始手写签名
NTKO.beginHandSign = function (key)
{
    if(ntkoObj.docOpen)
    {
        if(ntkoObj.obj.IsNTKOSecSignInstalled())
            ntkoObj.obj.AddSecHandSign(ntkoObj.user, 0, 0, 1);
        else
            ntkoObj.obj.DoHandSign(ntkoObj.user, 0, 0x000000ff, 2, 100, 50, null, key);
    }
}

//开始全屏手写签名
NTKO.beginFullScreenHandSign = function (key)
{
    if(ntkoObj.docOpen)
    {
        if(ntkoObj.obj.IsNTKOSecSignInstalled())
            ntkoObj.obj.AddSecHandSign(ntkoObj.user, 0, 0, 1);
        else
            ntkoObj.obj.DoHandSign2(ntkoObj.user, key, 0, 0, 0, 100);
    }
}

//开始手工绘图，可用于手工批示
NTKO.beginHandDraw = function ()
{
    if(ntkoObj.docOpen)
    {
        ntkoObj.obj.DoHandDraw(
            0,//笔型0－实线 0－4 //可选参数
            0x00ff0000,//颜色 0x00RRGGBB//可选参数
            3,//笔宽//可选参数
            200,//left//可选参数
            50//top//可选参数
            );
    }
}

//开始全屏手工绘图，可用于手工批示
NTKO.beginFullScreenHandDraw = function ()
{
    if(ntkoObj.docOpen)
    {
        ntkoObj.obj.DoHandDraw2();
    }
}

//检查签名结果 验证签名及印章
NTKO.doCheckSign = function (key)
{
    if(ntkoObj.docOpen)
    {
        var ret = ntkoObj.obj.DoCheckSign(false,key);

    //可选参数 IsSilent 缺省为FAlSE，表示弹出验证对话框,否则，只是返回验证结果到返回值
    }
}
NTKO.autoDocHeader = function(url,docType,ext) {
    try{
        //选择对象当前文档的所有内容
        var BookMarkName = "正文";
        var obj = ntkoObj.obj;
        if(obj.ActiveDocument.BookMarks.Exists(BookMarkName)) { 
            obj.ActiveDocument.Application.Selection.GoTo(-1,0,0,BookMarkName);
            var mark = obj.ActiveDocument.Bookmarks(BookMarkName);
            var range = mark.Range;
            range.Cut(); 
            obj.ActiveDocument.Application.Selection.HomeKey(6);//光标定位到文件头
            var curSel = obj.ActiveDocument.Application.Selection;curSel.WholeStory();
            curSel.Delete(); 
        } else {
            var curSel = obj.ActiveDocument.Application.Selection;curSel.WholeStory();
            curSel.Cut();
        }
        if(docType == 2){
            obj.BeginOpenFromURL(url,true,false);
            var fileName = ntkoObj.fileName.substr(0, ntkoObj.fileName.lastIndexOf("."));
            if(ext === 'dot'){
                ntkoObj.fileName = fileName + '.doc';
            } else {
                ntkoObj.fileName = fileName + '.' +ext;
            }
        }else{
            obj.AddTemplateFromURL(url);
        }
        NTKO.addDocHeaderExec();        
    } catch(err) {
        alert("错误：" + err.number + ":" + err.description);
    }
} 
//以下为以前版本的函数和实用函数
//此函数用来加入一个自定义的文件头部
NTKO.addDocHeader = function (url,docType,ext)
{
    try{
        //选择对象当前文档的所有内容
        var curSel = ntkoObj.obj.ActiveDocument.Application.Selection;
        NTKO.setReviewMode(false);
        curSel.WholeStory();
        curSel.Cut();
        //ntkoObj.docOpen = false;
        //插入模板
        //        url="/module/word_model/view/get.php?docID="+docID;
        if(docType == 2){
            ntkoObj.obj.BeginOpenFromURL(url,true,false);
            var fileName = ntkoObj.fileName.substr(0, ntkoObj.fileName.lastIndexOf("."));
            if(ext === 'dot'){
                ntkoObj.fileName = fileName + '.doc';
            } else {
                ntkoObj.fileName = fileName + '.' +ext;
            }
        }else{
            ntkoObj.obj.AddTemplateFromURL(url);
        }
        //NTKO.setReadOnly(false);
        NTKO.addDocHeaderExec();
    }
    catch(err)
    {
        _originalAlert(err.number + ":" + err.description);
    }
}

NTKO.addDocHeaderExec = function ()
{
    try{
        if(!ntkoObj.docOpen)
        {
            window.setTimeout(NTKO.addDocHeaderExec,200);
            return;
        }

        var BookMarkName = "正文";
        if(!ntkoObj.obj.ActiveDocument.BookMarks.Exists(BookMarkName))
        {
            var msg1 = sprintf("Word 模板中不存在名称为：'%s'的书签！",BookMarkName);
            _originalAlert(msg1+"\n"+"关于套红模版制作，请咨询技术支持人员。");
        } else {
            var bkmkObj = ntkoObj.obj.ActiveDocument.BookMarks(BookMarkName);
            var saverange = bkmkObj.Range
            saverange.Paste();
            ntkoObj.obj.ActiveDocument.Bookmarks.Add(BookMarkName,saverange);
        }
        //工作流套用表单数据
        //@test
        NTKO.addFlowData();
        NTKO.setReviewMode(true);
    }
    catch(err)
    {
        _originalAlert( err.number + ":" + err.description);
    }
}

NTKO.addFlowData = function () {
    if(ntkoObj.formUrl) {
        $.ajax({
            type:'post',
            async:false,
            url:ntkoObj.formUrl,
            dataType:'json',
            success:function(formData){
                $.each(formData, function(name,value){
                    if(name != '正文' && ntkoObj.obj.GetBookmarkValue(name) !== 'undefined'){
                        ntkoObj.obj.SetBookmarkValue(name,value);
                    }
                });
                if(ntkoObj.obj.GetBookmarkValue('当前日期') !== 'undefined'){
                    ntkoObj.obj.SetBookmarkValue('当前日期', TDate.getDate());
                }
            }
        });
    }
}

//如果原先的表单定义了OnSubmit事件，保存文档时首先会调用原先的事件。
NTKO.doFormOnSubmit = function ()
{
    var form = document.forms[0];
    if (form.onsubmit)
    {
        var retVal = form.onsubmit();
        if (typeof retVal == "boolean" && retVal == false)
            return false;
    }
    return true;
}

/*此函数在较低版本的IE浏览器中，用来代替
//Javascript的escape函数。
function TANGER_OCX_encodeObjValue(value)
{
	var t;
	t = value.replace(/%/g,"%25");
	return(t.replace(/&/g,"%26"));
}
*/
//此函数用来产生自动将表单数据创建成为
//控件的SaveToURL函数所需要的参数。返回
//一个paraObj对象。paraObj.FFN包含表单的
//最后一个<input type=file name=XXX>的name
//paraObj.PARA包含了表单的其它数据，比如：
//f1=v1&f2=v2&f3=v3.其中,v1.v2.v3是经过
//Javascript的escape函数编码的数据。如果IE
//的版本较低，可以使用上面注释掉的TANGER_OCX_encodeObjValue
//函数代替下面的escape函数。
NTKO.genDominoPara = function (paraObj)
{
    var fmElements = document.forms[0].elements;

    var i,j,elObj,optionItem;
    for (i=0;i< fmElements.length;i++ )
    {
        elObj = fmElements[i];
        switch(elObj.type)
        {
            case "file":
                paraObj.FFN = elObj.name;
                break;
            case "reset":
                break;
            case "radio":
            case "checkbox":
                if (elObj.checked)
                {
                    paraObj.PARA += ( elObj.name+"="+escape(elObj.value)+"&");
                }
                break;
            case "select-multiple":
                for(j=0;j<elObj.options.length;j++)
                {
                    optionItem = elObj.options[j];
                    if (optionItem.selected)
                    {
                        paraObj.PARA += ( elObj.name+"="+escape(optionItem.value)+"&");
                    }
                }
                break;
            default: // text,Areatext,selecte-one,password,submit,etc.
                if(elObj.name)
                {
                    paraObj.PARA += ( elObj.name+"="+escape(elObj.value)+"&");
                }
                break;
        }
    }
}

//设置文档为只读
NTKO.setReadOnly = function (boolvalue)
{
    var appName,i;
    try
    {
        if (boolvalue) ntkoObj.obj.IsShowToolMenu = false;

        if(!ntkoObj.docOpen)
            return;

        with(ntkoObj.obj.ActiveDocument)
        {
            appName = new String(Application.Name);
            if( (appName.toUpperCase()).indexOf("WORD") > -1 ) //Word
            {
                if (ProtectionType != -1 &&  !boolvalue)
                {
                    Unprotect();
                }
                if (ProtectionType == -1 &&  boolvalue)
                {
                    Protect(3,true,"");
                }
            }
            else if ( (appName.toUpperCase()).indexOf("EXCEL") > -1 ) //EXCEL
            {
                for(i=1;i<=Application.Sheets.Count;i++)
                {
                    if(boolvalue)
                    {
                        Application.Sheets(i).Protect("",true,true,true);
                    }
                    else
                    {
                        Application.Sheets(i).Unprotect("");
                    }
                }
                if(boolvalue)
                {
                    Application.ActiveWorkbook.Protect("",true);
                }
                else
                {
                    Application.ActiveWorkbook.Unprotect("");
                }
            }
            else
            {
            }
            }
    }
    catch(err){
        _originalAlert("错误：" + err.number + ":" + err.description);
    }
    finally{
    }
}

//允许或禁止用户从控件拷贝数据
NTKO.isNoCopy = function (boolvalue)
{
    ntkoObj.obj.IsNoCopy = boolvalue;
}

//允许或禁止文件－>新建菜单
NTKO.fileNew = function (boolvalue)
{
    ntkoObj.obj.FileNew = boolvalue;

}
//允许或禁止文件－>打开菜单
NTKO.fileOpen = function (boolvalue)
{
    ntkoObj.obj.FileOpen = boolvalue;
}
//允许或禁止文件－>保存菜单
NTKO.fileSave = function (boolvalue)
{
    ntkoObj.obj.FileSave = boolvalue;
}
//允许或禁止文件－>另存为菜单
NTKO.fileSaveAs = function (boolvalue)
{
    ntkoObj.obj.FileSaveAs = boolvalue;
}
//允许或禁止文件－>打印菜单
NTKO.filePrint = function (boolvalue)
{
    ntkoObj.obj.FilePrint = boolvalue;
}
//允许或禁止文件－>打印预览菜单
NTKO.filePrintPreview = function (boolvalue)
{
    ntkoObj.obj.FilePrintPreview = boolvalue;
}
//允许或禁止工具栏－>打印
NTKO.filePrintToolbar = function (boolvalue)
{
    try{
        ntkoObj.obj.ActiveDocument.CommandBars("Standard").Controls("打印(&P)").Enabled = boolvalue;//打印
        ntkoObj.obj.ActiveDocument.CommandBars("Standard").Controls("打印预览(&V)").Enabled = boolvalue;//打印预览
        ntkoObj.obj.FilePrint = boolvalue;
        ntkoObj.obj.FilePrintPreview = boolvalue;
    }
    catch(ex)
    {}
}
//允许或禁止工具栏－>打印预览
NTKO.filePrintPreviewToolbar = function (boolvalue) { }

//允许或禁止显示修订工具栏和工具菜单（保护修订）
NTKO.reviewBar = function (boolvalue)
{

    if(!ntkoObj.docOpen)
        return;
    ntkoObj.obj.ActiveDocument.CommandBars("Reviewing").Enabled = boolvalue;
    ntkoObj.obj.ActiveDocument.CommandBars("Track Changes").Enabled = boolvalue;
    ntkoObj.obj.IsShowToolMenu = boolvalue;	//关闭或打开工具菜单
}

//打开或者关闭修订模式 进入或退出痕迹保留状态，调用上面的两个函数
NTKO.setReviewMode = function (boolvalue)
{
    if(!ntkoObj.docOpen)
        return;
    try{
        ntkoObj.obj.ActiveDocument.TrackRevisions = boolvalue;
    }
    catch(ex)
    {}
}

//显示/不显示修订文字
NTKO.showRevisions = function (boolvalue)
{
    if(!ntkoObj.docOpen)
        return;
    try{
        ntkoObj.obj.ActiveDocument.ShowRevisions = boolvalue;
    }
    catch(ex)
    {}
}

//打印/不打印修订文字
NTKO.setPrintPrevisions = function (boolvalue)
{
    if(!ntkoObj.docOpen)
        return;
    ntkoObj.obj.ActiveDocument.PrintRevisions = boolvalue;
}

//设置用户名
NTKO.setDocUser = function (cuser)
{
    if(!ntkoObj.docOpen)
        return;
    with(ntkoObj.obj.ActiveDocument.Application)
    {
        UserName = cuser;
        }
}

//设置页面布局
NTKO.setLayout = function ()
{
    NTKO.showDoc();
    try
    {
        if (ntkoObj.obj.ActiveDocument != null)
            ntkoObj.obj.ShowDialog(5); //设置页面布局
    }
    catch(err){
        if(err.number!=-2147467260)
            _originalAlert(err.number + ":" + err.description);
    }
    finally{
    }
}

//打印文档
NTKO.printDoc = function ()
{
    NTKO.showDoc();
    try
    {
        if (ntkoObj.obj.ActiveDocument != null)
            ntkoObj.obj.printout(true);
    }
    catch(err){
        if(err.number!=-2147467260)
            _originalAlert(err.number + ":" + err.description);
    }
    finally{
    }
}
//此函数在网页装载时被调用。用来获取控件对象并保存到ntkoObj.obj
//同时，可以设置初始的菜单状况，打开初始文档等等。
NTKO.init = function () {
    var info = '';
    var ocxObj = ntkoObj.obj;//document.getElementById('TANGER_OCX');//document.all.item("TANGER_OCX");
    var useUTF8 = (document.charset == "utf-8");
    ocxObj.IsUseUTF8Data = useUTF8;
    ocxObj.FileNew = false;
    ocxObj.FileClose = false;
    ocxObj.FileOpen = ntkoObj.op == '4' ? true : false;
    ocxObj.FileSave = false;
    ocxObj.FileSaveAs = false;

    try
    {
        var fileExtName = ntkoObj.fileName.substr(ntkoObj.fileName.lastIndexOf(".")).toLowerCase();
        if(fileExtName === '.pdf' || fileExtName === '.tif' || fileExtName === '.tiff')
        {
            ocxObj.AddDocTypePlugin('.pdf','PDF.NtkoDocument','4.0.0.2',ntkoObj.ntkopdfdocUrl,51,true);
            ocxObj.AddDocTypePlugin('.tif','TIF.NtkoDocument','4.0.0.2',ntkoObj.ntkopdfdocUrl,52);
            ocxObj.AddDocTypePlugin('.tiff','TIF.NtkoDocument','4.0.0.2',ntkoObj.ntkopdfdocUrl,52);
        }

        re=/&amp;/g;
        ntkoObj.attachURL=ntkoObj.attachURL.replace(re,"&");

        if (ocxObj.IsHiddenOpenURL)
        {
            ntkoObj.attachURL = TANGER_OCX_HiddenURL(ntkoObj.attachURL);
        }

        switch(ntkoObj.op)
        {
            case "1":
                info = "新Word文档";
                ocxObj.CreateNew("Word.Document");
                break;
            case "2":
                info = "新Excel工作表";
                ocxObj.CreateNew("Excel.Sheet");
                break;
            case "3":
                info = "新PowserPoint幻灯片";
                ocxObj.CreateNew("PowerPoint.Show");
                break;
            case "4":
                info = "编辑文档";
                if(ntkoObj.attachURL)
                {
                    ocxObj.BeginOpenFromURL(ntkoObj.templateURL ? ntkoObj.templateURL : ntkoObj.attachURL,true,false);
                }
                else
                {
                    ocxObj.CreateNew("Word.Document");
                }
                if(ocxObj.IsNTKOSecSignInstalled() && document.getElementById("tr_ekey"))
                    document.getElementById("tr_ekey").style.display="";
                break;
            case "5":
            case "6":
            case "7":
                info = "阅读文档";
                if(ntkoObj.attachURL)
                {
                    ocxObj.BeginOpenFromURL(ntkoObj.attachURL,true,false);
                }
                break;
            default:
                info = "未知操作";
        }

    }
    catch(err){
        var msg='不能使用微软Office软件打开文档！\n\n是否尝试使用金山WPS文字处理软件打开文档？';
        if(typeof(ocxObj.BeginOpenFromURL) == 'function') {
            if(window._originalConfirm(msg))
            {
                if(ntkoObj.op==4)
                    ocxObj.BeginOpenFromURL(ntkoObj.attachURL,true,false,"WPS.Document");
                else
                    ocxObj.BeginOpenFromURL(ntkoObj.attachURL,true,true,"WPS.Document");
            }
        }
    }
    finally{
    }
}
//此函数在文档关闭时被调用。
NTKO.onDocumentClosed = function () {
    ntkoObj.docOpen = false;//ntkoObj.docOpen = false;
}
NTKO.autoSave = function(){
    if(ntkoObj.docOpen){
        ntkoObj.silentMode = true;
        NTKO.saveDoc('auto');
        ntkoObj.silentMode=false;
        setTimeout("NTKO.autoSave()", 1000*autoSaveInterval);   
    }
 
}
//此函数用来保存当前文档。主要使用了控件的SaveToURL函数。
//有关此函数的详细用法，请参阅编程手册。
NTKO.saveDoc = function (op_flag)
{
    var retStr=new String;
    var newwin,newdoc;
    var paraObj = new Object();
    paraObj.PARA="";
    paraObj.FFN ="";
    try
    {
        //if(!NTKO.doFormOnSubmit())return;
        //document.forms[0].DOC_SIZE.value = ntkoObj.obj.DocSize;
        $('input[name="DOC_SIZE"]').val(ntkoObj.obj.DocSize);
        NTKO.genDominoPara(paraObj);

        if(!paraObj.FFN)
        {
            _originalAlert("参数错误：控件的第二个参数没有指定。");
            return;
        }
        if(!ntkoObj.docOpen)
        {
            _originalAlert("没有打开的文档。");
            return;
        }
        switch(ntkoObj.op)
        {
            case "1":
                retStr = ntkoObj.obj.SaveToURL(ntkoObj.actionURL,paraObj.FFN,"",ntkoObj.fileName,0);
                if(retStr == 'OK') {
                    window._originalAlert('文档 '+ "'" + ntkoObj.fileName+ "'" + ' 保存成功！');
                //ntkoObj.docOpen = false;
                }
                break;
            case "2":
                retStr = ntkoObj.obj.SaveToURL(ntkoObj.actionURL,paraObj.FFN,"",ntkoObj.fileName,0);
                document.all("ATTACHMENT_ID").value=retStr;
                if(op_flag==1)
                {
                    ntkoObj.docOpen = false;
                    window.close();
                }
                break;
            case "3":
                retStr = ntkoObj.obj.SaveToURL(ntkoObj.actionURL,paraObj.FFN,"",ntkoObj.fileName,0);
                document.all("ATTACHMENT_ID").value=retStr;
                if(op_flag==1)
                {
                    ntkoObj.docOpen = false;
                    window.close();
                }
                break;
            case "4":
                //解决单元格出于激活状态时保存不了数据的问题 modified by lx 20100914
                var appName = new String(ntkoObj.obj.ActiveDocument.Application.Name);
                if ( (appName.toUpperCase()).indexOf("EXCEL") > -1 || appName.indexOf("EXCEL") > -1) //EXCEL
                {
                    ntkoObj.obj.Activedocument.ActiveSheet.select();
                }
                retStr = ntkoObj.obj.SaveToURL(ntkoObj.actionURL,paraObj.FFN,paraObj.PARA,ntkoObj.fileName,0,(op_flag==='auto' ? false : true));//非上传保存有问题
                retStr = $.parseJSON(retStr);
                if(retStr.flag == 'OK') {
                    ntkoObj.aipURL = retStr.aipUrl;
                    ntkoObj.fileName = retStr.fileName;
                    ntkoObj.actionURL = retStr.actionUrl;
                    if(Operation && typeof(Operation.setStatus) === 'function'){
                        Operation.setStatus(1);
                    }
                    if (!ntkoObj.silentMode) {
                        window._originalAlert('文档 ' + "'" + ntkoObj.fileName + "'" + ' 保存成功！');
                    }
                }
                if (op_flag != 5) {//保存不提示成功
                //window.alert(retStr);
                }
                if(op_flag == 1)
                {
                    ntkoObj.docOpen = false;
                    window.close();
                }
                break;
            case "5":
            case "6":
            case "7":
                _originalAlert("文档处于阅读状态，您不能保存到服务器。");
            default:
                break;
        }
    }
    catch(err){
        _originalAlert( "不能保存到URL：" + err.description);//"不能保存到URL："
    }
    finally{
    }
}

//此函数在文档打开时被调用。
NTKO.onDocumentOpened = function (str, obj) {
    if(!str.match('^http\:\/\/.+$') && !(str.indexOf('/templateOffice/') > 0)) {
        var pathArray = str.split('\\');
        ntkoObj.fileName = pathArray[pathArray.length-1];
    } else if(ntkoObj.templateName){
        ntkoObj.fileName = ntkoObj.templateName;
    }
    var s, s2;
    try
    {
        ntkoObj.docOpen = true;
        if( 0==str.length) {
            str = ntkoObj.fileName;
        }
        ntkoObj.obj.Caption = ntkoObj.fileName;//ntkoObj.obj.Caption = TANGER_OCX_filename;
        //ntkoObj.obj.IsResetToolbarsOnOpen = true; //重置工具栏为默认项
        if(ntkoObj.fileName.indexOf(".ppt")<0 && ntkoObj.fileName.indexOf(".PPT")<0 )
            NTKO.setDocUser(ntkoObj.user);//TANGER_OCX_user
        s = "未知应用程序";
        if(obj)
        {
            switch(ntkoObj.op)//TANGER_OCX_strOp
            {
                case "1":
                case "2":
                case "3":
                case "4":
                    NTKO.tdShowRevisions(ntkoObj.showRevisions);//是否显示痕迹
                    NTKO.tdSetMartModify(ntkoObj.trackRevisions);//是否保留痕迹
                    NTKO.setReadOnly(false);
                    break;
                case "5":
                case "6":
                case "7":
                    NTKO.tdShowRevisions(ntkoObj.showRevisions);//是否显示痕迹
                    NTKO.setReadOnly(false);
                    //针对WPS，禁止工具栏的输出为PDF格式和发送邮件
                    if(ntkoObj.op!="7")
                    {
                        var cmdbar = ntkoObj.obj.ActiveDocument.Application.CommandBars("Standard");
                        try
                        {
                            if(cmdbar.Controls("输出为PDF格式(&F)..."))//输出为PDF格式
                                cmdbar.Controls("(&F)...").Enabled = false;
                            if(cmdbar.Controls("(&F)..."))//输出为 PDF 格式
                                cmdbar.Controls("(&F)...").Enabled = false;
                            if(cmdbar.Controls("发送邮件(&D)..."))//发送邮件
                                cmdbar.Controls("(&D)...").Enabled = false;
                        }
                        catch(err){}
                    }

                    if(ntkoObj.fileName.toLowerCase().indexOf(".xls")>0)
                    {
                        var sheets = ntkoObj.obj.ActiveDocument.Sheets;
                        var sc = sheets.Count;
                        for(var i=1;i<=sc;i++)
                        {
                            sheets(i).EnableSelection = 1;
                        }
                    }

                    break;
                default:
                    break;
            }
            s = obj.Application.Name;
        }
    }
    catch(err){
        window.status = "OnDocumentOpened事件的Script产生错误" + err.number + ":" + err.description;//"OnDocumentOpened事件的Script产生错误。"
    }
    finally{
    }
}

NTKO.onSignSelect = function (issign,signinfo)
{
    if(!issign)
        return;

    if(signinfo.indexOf("用户:"+ntkoObj.user) == -1)//"用户:"
    {
        NTKO.setReadOnly(true);
        NTKO.setReadOnly(false);
    }
}

//保存文档为PDF到本地
NTKO.saveAsPDFFile = function (IsPermitPrint, IsPermitCopy)
{
    try{
        ntkoObj.obj.SaveAsPDFFile(
            '',
            true,
            '',
            true,
            false,
            '',
            IsPermitPrint,
            IsPermitCopy
            );
    }
    catch(err){
        if(err.number == -2147467259)
            _originalAlert("该功能需要软件PDFCreator支持\n请下载安装")+"http://www.go2oa.com/oa/PDFCreator-0_9_5_setup.exe";//"该功能需要软件PDFCreator支持\n请下载安装 http://www.go2oa.com/oa/PDFCreator-0_9_5_setup.exe"
    }
}

//保存文档为PDF文件
NTKO.savePDFToServer = function ()
{
    if(!ntkoObj.docOpen)
    {
        _originalAlert("没有打开的文档。");
        return;
    }
    try
    {
        ntkoObj.obj.PublishAsPDFToURL(
            "uploadpdf.php",
            ntkoObj.fileName.substr(0,ntkoObj.fileName.lastIndexOf("."))+".pdf",
            "form1",
            null, //sheetname,保存excel的哪个表格
            false, //IsShowUI,是否显示保存界面
            false, // IsShowMsg,是否显示保存成功信息
            false, // IsUseSecurity,是否使用安全特性
            null, // OwnerPass,安全密码.可直接传值
            false,//IsPermitPrint,是否允许打印
            true //IsPermitCopy,是否允许拷贝
            );
    }
    catch(err){
        _originalAlert( "不能保存PDF到URL："+ err.number + ":" + err.description);//"不能保存PDF到URL："
    }
    finally{
    }
}

NTKO.showOperationBar = function ()
{
    if(!$('td_menu') || !$('td_control')) return;
    if($('td_menu').style.display == "none")
    {
        $('td_menu').style.display = "";
        $('td_control').style.display = "none";
    }
    else
    {
        $('td_menu').style.display = "none";
        $('td_control').style.display = "";
    }
}

NTKO.tdSetMartModify = function (flag)
{
    NTKO.setReviewMode(flag == 0 ? false : true);
}

NTKO.tdShowRevisions = function (flag)
{
    NTKO.showRevisions(flag == 0 ? false : true);
}

NTKO.selSign = function SelSign()
{
    var SelSign=document.getElementById("SelSign");
    if(SelSign.style.display=="")
        SelSign.style.display="none";
    else
        SelSign.style.display="";
}

NTKO.selectWord = function (SEC_OC_MARK,DOT_TPL_STR)
{
    myleft=(screen.availWidth-650)/2;
    window.open(loadTemplateUrl,"formul_edit","height=350,width=600,status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
    return false;
//    if(typeof(DOT_TPL_STR)== 'undefined' || DOT_TPL_STR == ''){
//        URL="../word_model/view/index.php?SEC_OC_MARK="+SEC_OC_MARK;
//    }else{
//        URL="../word_model/view/index.php?SEC_OC_MARK="+SEC_OC_MARK+"&DOT_TPL_STR="+DOT_TPL_STR;
//    }
//    myleft=(screen.availWidth-650)/2;
//    window.open(URL,"formul_edit","height=350,width=400,status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left="+myleft+",resizable=yes");
}

NTKO.selSignFromURL = function (attchment_id,div_id,dir_field,name_field,disk_id)
{
    URL="/module/sel_file/?EXT_FILTER=esp&DIV_ID=" + div_id + "&DIR_FIELD=" + dir_field + "&NAME_FIELD=" + name_field + "&TYPE_FIELD=" + disk_id;
    loc_x=event.clientX+100;
    loc_y=event.clientY-100;
    window.open(URL,attchment_id,"height=300,width=500,status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top="+loc_y+",left="+loc_x+",resizable=yes");
}

NTKO.showDoc = function ()
{
    //    $('OC_LOG').style.display = "none";
    //    $('TANGER_OCX').style.display = "block";
    //    $('OC_HISTORY').style.display = "none";
    }
NTKO.showLog = function ()
{

    NTKO.getLog();

}
NTKO.getLog = function() {
    myleft = (screen.availWidth - 650) / 2;
    window.open(loadLogUrl, "doc_op_log", "height=350,width=600,status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left=" + myleft + ",resizable=yes");
    return false;
}
NTKO.showHistory = function ()
{
    NTKO.getHistory();
}
NTKO.getHistory = function() {
    myleft = (screen.availWidth - 650) / 2;
    window.open(loadHistoryUrl, "doc_histroy", "height=350,width=600,status=0,toolbar=no,menubar=no,location=no,scrollbars=yes,top=150,left=" + myleft + ",resizable=yes");
    return false;
}
NTKO.clearCopy = function() {
    ntkoObj.obj.ActiveDocument.Application.WordBasic.AcceptAllChangesInDoc();
    NTKO.saveDoc(0);
}
NTKO.beforeConvertToAip = function() {

//    var rs = ntkoObj.ActiveDocument.Revisions;
    ntkoObj.silentMode = true;
    NTKO.clearCopy();
    ntkoObj.silentMode = false;
//    ntkoObj.ActiveDocument.Application.WordBasic.AcceptAllChangesInDoc();
//    var rcount = rs.Count;
//    var rsinfor = "";
//    for (var i = 1; i <= rcount; i++) {
//        /*
//         * Type返回的是数字类型(0-13)：
//         * 0：表示没有痕迹
//         * 1：表示插入修订
//         * 2：表示删除修订
//         * 3：字体格式更改
//         * 10:带格式修订
//         */
//        rsinfor = "痕迹用户名：" + rs(i).Author + ",修订时间：" + rs(i).Date;
//        if (rs(i).Author === NTKO.user) {
//            if (rs(i).Type == 1) {
//                rsinfor = rsinfor + ",插入内容：" + rs(i).Range.Text;
//            } else if (rs(i).Type == 2) {
//                rsinfor = rsinfor + ",删除内容：" + rs(i).Range.Text;
//            } else {
//                rsinfor = rsinfor + ",修订内容：" + rs(i).Range.Text + "," + rs(i).FormatDescription;
//            }
//            alert(rsinfor);
//        }
//    }
}
NTKO.convertToAip = function() {
    NTKO.beforeConvertToAip();
    if (_originalConfirm('确认要将正文转为版式文件？')) {
        if ($(window.parent.document).find('#tab_aip_frame').length > 0) {
            window.parent.aipUrl = ntkoObj.aipURL;
            $(window.parent.document).find('#tab_aip_frame').attr('src', '');
            if($(window.parent.document).find('a[href="#tab_aip"]').length > 0) {
                window.parent.docM.switchTab('tab_aip');
            } else {
                window.parent.docM.switchTab('tab_form');
            }
        } else {
            _originalAlert("无版式文件显示权限，请联系管理员！");
        }
    }
}
function extend(des, src, override) {
    if (src instanceof Array) {
        for (var i = 0, len = src.length; i < len; i++)
            extend(des, src[i], override);
    }
    for (var i in src) {
        if (override || !(i in des)) {
            des[i] = src[i];
        }
    }
    return des;
}
function sprintf()
{
    var arg = arguments,
    str = arg[0] || '',
    i, n;
    for (i = 1, n = arg.length; i < n; i++) {
        str = str.replace(/%s/, arg[i]);
    }
    return str;
}

$(document).ready(function(){
    $(window).resize(function(){
        var height = $(window).height();
        var toolbarHeight = $('#toolbars').outerHeight();
        $('#TANGER_OCX, #ntkosidebar').height(height - toolbarHeight);
    }).trigger('resize');
});
