/**
 * 定义AIP对象
 * @param {string} container 加载AIP的容器id
 * @param {json} option 可选的参数，参考def的定义
 * @returns {AIP}
 */
function AIP(container,option) {
    this.obj = null;
    this.mapData = null;
    this.pdf417url = null;
    this.container = container;
    var cfg = {
        id : "0",
        prefix : "AIP_",
        codebase: "/static/activex/HWPostil_trial.cab",
        width : "100%",
        height : "100%",
        fileBae64 : "",         //文件base64数据
        fileURL : "",           //文件加载地址
        fileName : "",          //上传文件标识
        aipUploadUrl : "",      //AIP上传地址
        pdfUploadUrl : "",      //PDF上传地址
        pageUploadUrl : "",     //页数上传地址
        thumbnailUploadUrl : "",//首页缩略图上传地址
        uploadReturnCode : "+OK", //上传成功返回标识
        convert : 0,
        version : "3.1.1.4",
        clsid : "clsid:FF3FE7A0-0578-4FEE-A54E-FB21B277D567",
        showMenu : 0,           //菜单栏参数 0为隐藏; 1为显示
        showScrollBar: 1,       //滚动条及底部工具条参数: 0全显示; 1只显示滚动条; 2全隐藏
        showToolBar : 0,        //工具栏参数: 0为隐藏; 1为显示; 2大图标模式
        designMode : false,     //设计模式
        penColor : 0,           //默认画笔颜色
        penWidth : 3,           //默认画笔宽度
        pageMode : 2,           //页面显示模式，1:自定义模式,放大缩小比例根据sZoomPercent而定。2:适用宽度模式,sZoomPercent无效。4:适用窗口模式,sZoomPercent无效。8:多列显示模式,sZoomPercent指定排列的列数。16:平滑显示模式,sZoomPercent为1表示平滑显示;为0表示非平滑显示(一般用户编辑页和手写页)。32:播放模式,sZoomPercent为非0表示进入播放模式;为0表示退出播放模式。64:特殊模式
        pageModeParam: 100,     //页面模式参数,
        signContentPosition : 0, //会签意见位置
        loginUser:'',           //当前登录用户
        debug : false           //调试模式
    };
    this.noteType = {
        link: {name: 'link',value:'4'},
        pen: {name:'pen' , value:'10'},//手写、文字区域
        checkbox: {name:'checkbox', value:'240'},
        radio: {name:'radio', value:'241'},
        select: {name:'select', value:'242'}
    };

    this.noteValueType = {
        allData : 1, //所有数据
        text : 2,   //文本
        index : 3,  //顺序索引
        lineNumber: 4,  //编辑区域行号
        lineHeight: 5,  //编辑区域行高
        left: 6,        //坐标left
        top: 7,         //坐标top
        width: 8,       //宽度
        height: 9,       //高度
        hasText: 10,     //是否存在文本
        hasPostil: 11,   //是否存在签章
        type: 12,
        image: 14,
        hasImage: 15,
        pageIndex: 20,
        userName: 21,
        parentName: 22,
        createTime: 27
    };
    this.cfg = AIPHelper.extend(cfg, option, true);
    this.Init();

}

/**
 * 兼容加载了modal的alert美化处理
 * @param {type} msg
 * @returns {undefined}
 */
AIP.prototype.Alert = function(msg) {
    if(this.cfg.debug) {
        if (typeof window._originalAlert === "function" || typeof window._originalAlert === "object") {
            window._originalAlert(msg);
        } else {
            window.alert(msg);
        }
    }
}
/**
 * 初始化方法
 * @param {string} container 加载AIP的容器id
 * @returns {undefined}
 */
AIP.prototype.Init = function() {
    var s = "";
    s += '<object id="' + this.cfg.prefix + this.cfg.id
    + '" style="width:' + this.cfg.width
    + ';height:' + this.cfg.height
    + ';" codebase="' + this.cfg.codebase + '#' + this.cfg.version
    + '" classid="' + this.cfg.clsid + '">';
    s += '<param name="_Version" value="65536">';
    s += '<param name="_ExtentX" value="17410">';
    s += '<param name="_ExtentY" value="10874">';
    s += '<param name="_StockProps" value="0">';
    s += '</object>';
    s += '<input type="hidden" name="'+this.cfg.prefix+'DATA_' + this.cfg.id + '">';
    var DOMContainer = document.getElementById(this.container);
    if(DOMContainer) {
        $(DOMContainer).html(s);
    }
    this.obj = document.getElementById(this.cfg.prefix + this.cfg.id);
};

/**
 * 从URL加载文件
 * @param {string} url
 * @returns {undefined}
 */
AIP.prototype.LoadFile = function(url) {
    if (url !== "") {
        var ret = this.obj.LoadFile(url);
        if (ret !== 1) {
            this.Alert(this.GetErrDesc(ret));
        }
    }
};

/**
 * 从base64字符串加载文件
 * @param {string} strBase64
 * @returns {undefined}
 */
AIP.prototype.LoadFileBase64 = function(strBase64) {
    if (strBase64 !== "") {
        var ret = this.obj.LoadFileBase64(strBase64);
        if (ret !== 0) {
            this.Alert(this.GetErrDesc(ret));
        }
    }
};

/**
 * 控制加载完毕事件处理方法
 */
AIP.prototype.OnCtrlReady = function() {
    this.obj.ShowDefMenu = this.cfg.showMenu ;
    this.obj.ShowScrollBarButton = this.cfg.showScrollBar;
    this.obj.ShowToolBar = this.cfg.showToolBar;
    this.obj.InDesignMode = this.cfg.designMode;
    this.obj.JSEnv = 1;
    this.obj.SetPageMode(this.cfg.pageMode, this.cfg.pageModeParam);
    if(this.cfg.fileBae64 !== "") {
        this.LoadFileBase64(this.cfg.fileBae64);
    } else if(this.cfg.fileURL !== "") {
        this.LoadFile(this.cfg.fileURL);
    }
    if($.cookie('AIP_PenColor')) {
        this.obj.CurrPenColor = $.cookie('AIP_PenColor');
    } else {
        this.obj.CurrPenColor = this.cfg.penColor;
    }
    if($.cookie('AIP_PenWidth')) {
        this.obj.CurrPenWidth = $.cookie('AIP_PenWidth');
    } else {
        this.obj.CurrPenWidth = this.cfg.penWidth;
    }
    if(this.mapData != null) {
        this.setValues();
    }
};
//set value @test
AIP.prototype.setValues = function(){
    var tObj = this.obj;
    var signContentPosition = this.cfg.signContentPosition;
    $.each(this.mapData, function(i,value){
        if(value) {
            tObj.SetValue('Page1.' + i,'');
            if(typeof value == 'object') {//会签数据
                var page = tObj.CurrPage;
                var StartX = tObj.GetNotePosX('Page1.' + i);
                var StartY = tObj.GetNotePosY('Page1.' + i);
                var xWidth = tObj.GetNoteWidth('Page1.' + i);
                var yHeight = tObj.GetNoteHeight('Page1.' + i);
                var row;
                $.each(value,function(k,sign){
                    var signNote = tObj.InsertNote('sign_'+k+i,page,3,StartX,StartY,xWidth,yHeight);
                    tObj.SetValue(signNote,sign['content']);
                    tObj.SetValue(signNote,":PROP::LABEL:2");
                    tObj.SetValue(signNote,":PROP:HALIGN:"+signContentPosition);
                    tObj.SetValue(signNote,":PROP:FONTSIZE:12");
                    tObj.SetValue(signNote,":PROP:FONTITALIC:0");
                    tObj.SetValue(signNote,":PROP:FONTUNDLINE:0");
                    row =  Math.ceil(sign['content'].length * 1016 / xWidth);
                    var userNote = tObj.InsertNote('user_'+k+i,page,3,StartX - 5300,StartY + row * 720,xWidth,yHeight);
                    tObj.SetValue(userNote,sign['user']);
                    tObj.SetValue(userNote,":PROP::LABEL:2");
                    tObj.SetValue(userNote,":PROP:HALIGN:2");
                    tObj.SetValue(userNote,":PROP:FONTSIZE:12");
                    tObj.SetValue(userNote,":PROP:FONTITALIC:0");
                    tObj.SetValue(userNote,":PROP:FONTUNDLINE:0");

                    var timeNote = tObj.InsertNote('time_'+k+i,page,3,StartX,StartY + row * 720,xWidth,yHeight);
                    tObj.SetValue(timeNote,sign['time']);
                    tObj.SetValue(timeNote,":PROP::LABEL:2");
                    tObj.SetValue(timeNote,":PROP:HALIGN:2");
                    tObj.SetValue(timeNote,":PROP:FONTSIZE:12");
                    tObj.SetValue(timeNote,":PROP:FONTITALIC:0");
                    tObj.SetValue(timeNote,":PROP:FONTUNDLINE:0");
                    StartY = StartY + (row+1) * 720 + 300;
                });
            } else {
                tObj.SetValue('Page1.' + i,value);
                tObj.SetValue('Page1.' + i,":PROP:BORDER:0");
            }
        }
        tObj.SetValue('Page1.' + i,":PROP::LABEL:2");
    });
}
/**
 * 文档打开事件处理方法
 *
 * @returns {undefined}
 */
AIP.prototype.OnDocOpened = function() {
    if(this.cfg.convert == 1) {
        this.Save();
    }
};

/**
 * 文件保存
 *
 * @returns {undefined}
 */
AIP.prototype.Save = function(mode, callback){
    var bRet = false;
    var msg = '';
    if(!(this.obj && this.obj.IsOpened && this.obj.IsOpened()))//文档是否打开
    {
        msg = "尚未加载AIP";
    } else {
        try {
            this.obj.HttpInit();                                //初始化HTTP引擎。
            this.obj.HttpAddPostCurrFile(this.cfg.fileName);    //设置上传当前文件,文件标识为FileBlod。

            //开始转化pdf
            if(this.cfg.pdfUploadUrl) {//不再保存pdf，移动端直接打开版式文件
                // 归档需要用到PDF正文，暂时不能去掉 by fl 10-29
                var pdfFileName = this.obj.GetTempFileName('pdf');
                if(pdfFileName) {
                    this.obj.saveTo(pdfFileName, 'pdf', 0);
                    this.obj.HttpAddPostFile('Attachment[pdf][]', pdfFileName);
                }
            }

            //生成缩略图
            if(this.cfg.thumbnailUploadUrl) {
                var thumbnailFileName = this.obj.GetTempFileName('jpg');
                if(thumbnailFileName) {
                    this.obj.saveTo(thumbnailFileName, 'jpg', 0);
                    this.obj.HttpAddPostFile('Attachment[thumbnail][]', thumbnailFileName);
                }
            }

            var ret = this.obj.HttpPost(this.cfg.aipUploadUrl); //上传数据
            if(pdfFileName) {
                this.obj.DeleteLocalFile(pdfFileName);
            }
            if(thumbnailFileName) {
                this.obj.DeleteLocalFile(thumbnailFileName);
            }
            ret = eval('(' + ret + ')');
            if(ret.code !== this.cfg.uploadReturnCode) {
                msg = '保存文件失败！';
            } else {
                //保存页数
                if(this.cfg.pageUploadUrl) {
                    $.post(this.cfg.pageUploadUrl, {'page' : this.obj.PageCount});
                }
                //保存二维码
                var img = this.obj.getValueEx('BarCodeImg', 14, "bmp", 100, "");
                if(img) {
                    $.post(this.pdf417url, {name : 'BarCodeImg' + ".bmp", data : img}, function(){img = null;});
                }
                
                bRet = true;
                msg = '保存文件成功！';
            }
            if($.type(callback) === 'function'){
                callback(ret);
            }

        } catch(err) {
             msg = '保存文件异常！' + err.toString();
            if($.type(callback) === 'function'){
                callback({code:'-ERR', msg: msg});
            }
        }
    }

    if(typeof mode == "undefined" || mode !=='silent') {
        this.Alert(msg);
    }
    return bRet;
};

/**
 * 获取所有节点数据集合
 * @returns {json}
 */
AIP.prototype.GetNotesData = function() {
    var data = '{';
    var noteName;
    var noteType,noteTypeName;
    var noteValue;
    while(noteName = this.obj.GetNextNote("HWSEALDEMO", 0, noteName)){
        try {
            noteType = this.GetNoteType(noteName);
        } catch(e) {
            _originalAlert(e);
        }
        var key = noteName.split('.')[1];//AIPHelper.uniqueId();
        switch(noteType) {
            case this.noteType.link.value:
                noteTypeName = this.noteType.link.name;
                break;
            case this.noteType.pen.value:
                noteTypeName = this.noteType.pen.name;
                break;
            case this.noteType.radio.value:
                noteTypeName = this.noteType.radio.name;
                break;
            case this.noteType.checkbox.value:
                noteTypeName = this.noteType.checkbox.name;
                break;
            case this.noteType.select.value:
                noteTypeName = this.noteType.select.name;
                break;
            default :
                break;
        }
        noteValue = this.GetValue(noteName);//this.GetNoteValue(noteName);

        data += '"' + key + '":' + '{"name":"' + noteName + '", "type":"' + noteTypeName + '", "value":"' + noteValue + '"},';
    }
    data = data.substr(data.length - 1) == ',' ? data.substr(0, data.length - 1) : data;
    data += '}';
    return data;
};

/**
 * 获取节点值
 * @param {type} noteName
 * @returns {AIP.prototype@pro;obj@call;GetValueEx}
 */
AIP.prototype.GetNoteValue = function(noteName) {
    return this.obj.GetValueEx(noteName, this.noteValueType.allData, '', 0, '');
}
/**
 * 设置节点唯一标识
 * @param noteName
 * @param noteValue
 **/
AIP.prototype.SetNoteValue = function(noteName, noteValue) {
    this.obj.SetValue(noteName,noteValue);
}
/**
 * 获取节点类型
 * @param {type} noteName
 * @returns {AIP.prototype@pro;obj@call;GetValueEx}
 */
AIP.prototype.GetNoteType = function(noteName) {
    return this.obj.GetValueEx(noteName, this.noteValueType.type, '', 0, '');
};

/**
 * 获取节点大小
 * @param {type} noteName
 * @returns {AIP.prototype.GetNoteSize.Anonym$0}
 */
AIP.prototype.GetNoteSize = function(noteName) {
    return {
        width:this.obj.GetValueEx(noteName, this.noteValueType.width),
        height:this.obj.GetValueEx(noteName, this.noteValueType.height)
    };
};

/**
 * 获取节点位置
 * @param {type} noteName
 * @returns {AIP.prototype.GetNotePos.Anonym$1}
 */
AIP.prototype.GetNotePos = function(noteName) {
    return {
        left:this.obj.GetValueEx(noteName, this.noteValueType.left),
        top:this.obj.GetValueEx(noteName, this.noteValueType.top)
    };
};

/**
 * 登陆
 * @param {type} user
 * @param {type} type
 * @param {type} access
 * @param {type} pass
 * @returns {undefined}
 */
AIP.prototype.Login = function(user, type, access, pass) {
    var ret = this.obj.Login(user, type, access, pass, "");
    if(ret !== 0) {
        if(type === 1) {
            this.obj.Login("HWSEALDEMO**", 4, 65535, "DEMO", "");
        } else {
            this.Alert(this.GetErrDesc(ret));
        }
    }
};

/**
 * 设置大小
 * @param {type} width
 * @param {type} height
 * @returns {undefined}
 */
AIP.prototype.SetSize = function(width, height) {
    this.obj.width = width;
    this.obj.height = height;
    $('#'+this.container).css({'width':width, 'height':height});
};

/**
 * 设置Action，参考API
 * @param {type} action
 * @returns {undefined}
 */
AIP.prototype.SetCurrAction = function(action) {
    this.obj.CurrAction = action;
};

/**
 * 获取节点值
 * @param {type} name
 * @returns {unresolved}
 */
AIP.prototype.GetValue = function(name) {
    return this.obj.GetValue(name);
};

/**
 * 获取当前文件base64值，如果文件中有印章无法获取
 * @returns {unresolved}
 */
AIP.prototype.GetCurrFileBase64 = function() {
    return this.obj.GetCurrFileBase64();
};

/**
 * 节点是否存在
 * @param {type} noteName
 * @returns {Boolean}
 */
AIP.prototype.isNoteExist = function(noteName) {
    var tmpNote = null;
    while(tmpNote = this.obj.GetNextNote("", 0, tmpNote)){
        if(tmpNote == noteName) {
            return true;
        }
    }
    return false;
};

/**
 * 插入节点
 * @param {type} name
 * @param {type} page
 * @param {type} type
 * @param {type} posX
 * @param {type} posY
 * @param {type} w
 * @param {type} h
 * @param {type} props
 * @returns {unresolved}
 */
AIP.prototype.InsertNote = function(name, page, type, posX, posY, w, h, props) {
    if(name == "") {
        name = AIPHelper.uniqueId();
    }
    if(this.isNoteExist(name)) {
        this.Alert("节点名称已经存在");
    }
    if(!page) {
        page = this.obj.CurrPage;
    }
    if(!type) {
        type = 3;
    }
    var note = this.obj.InsertNote(name, page, type, posX, posY, w, h);
    if(note && props)
    {
        for(var propName in props) {
            this.obj.SetValue(note, ":PROP:"+propName+":"+props[propName]+"");
        }
    }

    return note;
};

/**
 * 设置笔色
 * @param {type} penColor
 * @returns {undefined}
 */
AIP.prototype.SetPenColor = function(penColor){
    $.cookie('AIP_PenColor',penColor);
    this.obj.CurrPenColor = penColor;
};

/**
 * 设置笔宽
 * @param {type} penWidth
 * @returns {undefined}
 */
AIP.prototype.SetPenWidth = function(penWidth){
    $.cookie('AIP_PenWidth',penWidth);
    this.obj.CurrPenWidth = penWidth;
};

/**
 * 缩放
 * @param {type} percent
 * @returns {undefined}
 */
AIP.prototype.Zoom = function(percent) {
    var currPageMode = this.obj.JSGetPageMode();
    var currZoomPercent = this.obj.JSValue;
    if(currPageMode == 1) {
        this.obj.SetPageMode(currPageMode,currZoomPercent + percent);
    }
};

AIP.prototype.ActHandWrite = function(type, url) {
    if(type == 1) {//USBKey
        if(this.obj.IsLogin() === 0) {
            var ret = this.Login(this.cfg.loginUser, 1 , 32767, "", "");
        }
    } else {//印章服务器
        var ret=this.obj.Login(this.cfg.loginUser, 3, 65535, "", url);
        if(ret != 0){
            _originalAlert('登录失败:' + ret);
            return false;
        }
    }
    this.obj.CurrAction = 264;
};

AIP.prototype.ActAddSeal = function(type, url) {
    if(type == 1) {//USBKey
        if(this.obj.IsLogin() === 0) {
            var ret = this.Login(this.cfg.loginUser, 1 , 32767, "", "");
        }
    } else {//印章服务器
        var ret=this.obj.Login(this.cfg.loginUser, 3, 65535, "", url);
        if(ret != 0){
            _originalAlert('登录失败:' + ret);
            return false;
        }
    }
    this.obj.CurrAction = 2568;
};

AIP.prototype.LoadScanFile = function() {
    if(this.obj.IsLogin() === 0) {
        this.Login(this.cfg.loginUser, 1 , 32767, "", "");
    }
    this.obj.InsertEmptyPage(0,3,0,0);
};

AIP.prototype.ActErase = function() {
    if(this.obj.IsLogin() === 0) {
        this.Login(this.cfg.loginUser, 1 , 32767, "", "");
    }
    this.obj.CurrAction = 16;
};

AIP.prototype.ActPdf417code = function(){
    var obj = this.obj;
    var targetUrl = this.pdf417url;
    var imgName = 'BarCodeImg';
    if(obj.IsLogin() === 0) {
        this.Login(this.cfg.loginUser, 1 , 32767, "", "");
    }
    if(obj.GetValue(imgName) != ''){
        obj.DeleteNote(imgName);
    }
    $.ajax({
        type:'get',
        url:targetUrl,
        dataType:'json',
        success:function(data){
            if(data) {
                obj.InsertPicture(imgName,data.basedata,obj.CurrPage,34000,46000,458852);
            }
        }
    });
}

/**
 * 错误提示
 * @param {type} ErrCode
 * @returns ｛string｝
 */
AIP.prototype.GetErrDesc = function(ErrCode) {
    switch(ErrCode) {
        case 0:
            return "正确";
        case -1:
            return "AIP服务器正忙，请稍候再试";
        case -2:
            return "服务器无效，请检查其是否已启动";
        case -3:
            return "出现未知的连接错误";
        case -5:
            return "无效的命令，可能系统不支持本操作";
        case -6:
            return "系统不支持本操作"
        case -7:
            return "错误的数据包格式，可能数据传输不正确";
        case -8:
            return "本机与服务器时间不符，被拒绝登录，请保证本机时间和服务器时间误差在十五分钟之内";

        case -11:
            return "指定证书已经被作废，无法使用";
        case -12:
            return "指定证书已经过期，无法使用";
        case -13:
            return "服务器数据库中未发现本用户对应的证书";

        case -20:
            return "权限错误，本用户无权使用指定的印章";
        case -21:
            return "指定印章已经被作废，无法使用";
        case -22:
            return "指定印章已经过期，无法使用";
        case -23:
            return "指定印章不存在";

        case -30:
            return "权限错误，你无权操作本文档";
        case -31:
            return "文档已经被锁定，你无权操作";
        case -33:
            return "指定文档不存在";
        case -35:
            return "权限错误，你无权打印本文档";
        case -36:
            return "文档打印份数已用完，你无权打印本文档";

        case -40:
            return "打开服务器数据库失败";
        case -41:
            return "更新服务器数据库失败";
        case -42:
            return "读取服务器数据库失败";
        case -43:
            return "操作服务器数据库失败";

        case -50:
            return "本用户还没有登录服务器，无法进行本操作";
        case -51:
            return "用户登录密码错误";
        case -52:
            return "本用户已被服务器作废";
        case -53:
            return "指定用户不存在";
        case -54:
            return "用户密码不符合格式，请联系管理员";
        case -55:
            return "未发现当前用户的证书信息";

        case -70:
            return "服务器未注册，请联系管理员";
        case -71:
            return "服务器上用户数多于授权数量，请联系管理员";
        case -72:
            return "本用户不属于本域服务器，请联系管理员";
        case -73:
            return "域服务器不支持此种登录方式";
        case -74:
            return "服务器端处理超时";
        case -75:
            return "服务器端发生未知错误，请联系管理员";

        case -100:
            return "域服务器地址无效，请保证其类似于IP:Port(直连)，或HTTP URL(如http://www.xx.com:8080/aipserver.jsp)";
        case -101:
            return "连接超时";
        case -102:
            return "用户取消";
        case -103:
            return "测试用户ID应该以'HWSEALDEMO'开始";
        case -104:
            return "未发现用户列表";

        case -110:
            return "有新用户登录";
        case -111:
            return "出现错误，操作被终止";

        case -120:
            return "无效的对象";
        case -121:
            return "无效数据错误";
        case -122:
            return "无效的窗口";
        case -123:
            return "无效的密码";

        case -130:
            return "身份校验出错";
        case -131:
            return "内存无法分配";

        case -140:
            return "错误的授权";
        case -141:
            return "错误的类型";
        case -142:
            return "未知错误";

        case -200:
            return "没有插入智能卡";
        case -201:
            return "错误的智能卡登录PIN码";
        case -202:
            return "系统未发现有效的私钥";
        case -203:
            return "系统未发现有效的证书";
        case -204:
            return "本机不存在CSP服务";
        case -205:
            return "本机存在多个智能卡";
        case -206:
            return "CSP驱动未安装,请确认已经安装了正确的智能卡驱动";
        case -209:
            return "操作智能卡过程中出现未知错误";

        case -210:
            return "身份验证未通过";
        case -211:
            return "智能卡中不存在印章";

        default :
            return "未知错误";
    }
};

var AIPHelper = {
    extend : function(des, src, override){
        if(src instanceof Array){
            for(var i = 0, len = src.length; i < len; i++)
                extend(des, src[i], override);
        }
        for( var i in src){
            if(override || !(i in des)){
                des[i] = src[i];
            }
        }
        return des;
    },
    uniqueId : function(){
        var a = Math.random();
        var b = (a + new Date().getTime());
        return b.toString(16).replace(".", "").substr(-6);
    }
};
