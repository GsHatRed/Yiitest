/*******************************************************
 *
 * 使用此JS脚本之前请先仔细阅读mToKen K1帮助文档!
 *
 * @version	4.0
 * @date	2015/9/22
 * @explanation	mToKen K1 V3.0 多浏览器支持
 *
 **********************************************************/
function isIe()
{
	return ("ActiveXObject" in window);
}

function mToken(obj){
	this.obj = obj;


	var g_mTokenPlugin = null;


	this.LoadLibrary = function()
	{
		//浏览器判断
		if(isIe() ){	//IE
			if(!-[1,]){	//IE678
				g_mTokenPlugin = document.getElementById(obj);
			}else{	//IE9+
				if(!!window.ActiveXObject){
					g_mTokenPlugin = document.getElementById(obj);
					g_mTokenPlugin.setAttribute("type", "application/x-npmplugin");
				}else {
					g_mTokenPlugin = new K1ClientPlugin();
				}

			}
		}else {
			g_mTokenPlugin = new K1ClientPlugin();
		}

		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return 0;
	};

	this.K1_mTokenGetVersion = function()
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetVersion();
	};

	this.K1_mTokenFindDevice = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenFindDevice();
	};

	this.K1_mTokenGetLastError = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGetLastError();
	};

	this.K1_mTokenGetUID = function(keyIndex)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetUID(keyIndex);
	};

	this.K1_mTokenOpen = function(keyUID, keyPassword)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenOpen(keyUID, keyPassword, 1);
	};

	this.K1_mTokenClose = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenClose();
	};

	this.K1_mTokenChangePwd = function(keyUID,oldPassword, newPassword)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenChangePwd(keyUID, 1, oldPassword, newPassword);
	};

	this.K1_mTokenSHA1WithSeed = function(keyUID, randomStr)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenSHA1WithSeed(keyUID, randomStr);
	};

	this.K1_mTokenGenResetPwdRequest = function(keyUID, userInfo)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGenResetPwdRequest(keyUID, userInfo);
	};

	this.K1_mTokenResetPassword = function(keyUID, serverResponse)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenResetPassword(keyUID, serverResponse);
	};

	this.K1_mTokenGenRandom = function(keyUID, randomLength)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGenRandom(keyUID, randomLength);
	};

	this.K1_mTokenReadSecureStorage = function(keyUID, offset, dataLength)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenReadSecureStorage(keyUID, offset, dataLength);
	};

	this.K1_mTokenWriteSecureStorag = function(keyUID, offset, writeData)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenWriteSecureStorage(keyUID, offset, writeData);
	};

	this.K1_mTokenReadUserStorage = function(keyUID, offset, dataLength)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenReadUserStorage(keyUID, offset, dataLength);
	};

	this.K1_mTokenWriteUserStorage = function(keyUID, offset, writeData)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenWriteUserStorage(keyUID, offset, writeData);
	};

	this.K1_mTokenGetURL = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetURL(keyUID);
	};

	this.K1_mTokenGetLabel = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetLabel(keyUID);
	};

	this.K1_mTokenGetCompanyName = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetCompanyName(keyUID);
	};

	this.K1_mTokenGetRemark = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetRemarks(keyUID);
	};
	this.K1_mTokenGetOpenType = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenGetOpenType(keyUID);
	};

	this.K1_mTokenPwdRetryCount = function(keyUID)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenPwdRetryCount(keyUID, 1);
	};
	this.K1_mTokenEncrypt = function(keyUID, method, data)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenEncrypt(keyUID,  method, 1,  data);
	};
	this.K1_mTokenDecrypt = function(keyUID,  method,  data)
	{
		if(g_mTokenPlugin == null)
		{
			return null;
		}

		return g_mTokenPlugin.mTokenDecrypt(keyUID,  method, 1, data);
	};

}



function K1ClientPlugin()
{
	var url = "http://127.0.0.1:51111/K1_Client";

	var xmlhttp ;
	function AjaxIO(json) {
		if(xmlhttp == null) {
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		xmlhttp.open("POST", url, false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("json=" + json);
	}

	this.mTokenGetVersion = function()
	{
		var json = '{"function":"mTokenGetVersion"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenFindDevice = function()
	{
		var json = '{"function":"mTokenFindDevice"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.devCount;
		}else{
			return -2;
		}
	};

	this.mTokenGetLastError = function()
	{
		var json = '{"function":"mTokenGetLastError"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.errorCode;
		}else{
			return -2;
		}
	};

	this.mTokenGetUID = function(keyIndex)
	{
		var json = '{"function":"mTokenGetUID", "keyIndex":' + keyIndex + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenOpen = function(keyUID, keyPassword, type)
	{
		var json = '{"function":"mTokenOpen", "keyUID":"' + keyUID + '", "passWd":"' + keyPassword + '", "passWdType":' + type + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenClose = function()
	{
		var json = '{"function":"mTokenClose"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenChangePwd = function(keyUID, type, oldPassword, newPassword)
	{
		var json = '{"function":"mTokenChangePwd", "keyUID":"' + keyUID + '", "oldUpin":"' + oldPassword + '", "newUpin":"' + newPassword + '", "passWdType":' + type + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenSHA1WithSeed = function(keyUID, randomStr)
	{
		var json = '{"function":"mTokenSHA1WithSeed", "keyUID":"' + keyUID + '", "random":"' + randomStr + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenGenResetPwdRequest = function(keyUID, userInfo)
	{
		var json = '{"function":"mTokenGenResetPwdRequest", "keyUID":"' + keyUID + '", "userInfo":"' + userInfo + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenResetPassword = function(keyUID, serverResponse)
	{
		var json = '{"function":"mTokenResetPassword", "keyUID":"' + keyUID + '", "response":"' + serverResponse + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenGenRandom = function(keyUID, randomLength)
	{
		var json = '{"function":"mTokenGenRandom", "keyUID":"' + keyUID + '", "inDataLen":' + randomLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenReadSecureStorage = function(keyUID, offset, dataLength)
	{
		var json = '{"function":"mTokenReadSecureStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inDataLen":' + dataLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenWriteSecureStorage = function(keyUID, offset, writeData)
	{
		var json = '{"function":"mTokenWriteSecureStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inData":"' + writeData + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenReadUserStorage = function(keyUID, offset, dataLength)
	{
		var json = '{"function":"mTokenReadUserStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inDataLen":' + dataLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenWriteUserStorage = function(keyUID, offset, writeData)
	{
		var json = '{"function":"mTokenWriteUserStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inData":"' + writeData + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenGetURL = function(keyUID)
	{
		var json = '{"function":"mTokenGetURL", "keyUID":"' + keyUID + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenGetLabel = function(keyUID)
	{
		var json = '{"function":"mTokenGetLabel", "keyUID":"' + keyUID + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenGetCompanyName = function(keyUID)
	{
		var json = '{"function":"mTokenGetCompanyName", "keyUID":"' + keyUID + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenGetRemarks = function(keyUID)
	{
		var json = '{"function":"mTokenGetRemarks", "keyUID":"' + keyUID + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenGetOpenType = function(keyUID)
	{
		var json = '{"function":"mTokenGetOpenType", "keyUID":"' + keyUID + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.openType;
		}else{
			return -1;
		}
	};

	this.mTokenPwdRetryCount = function(keyUID, passwdType)
	{
		var json = '{"function":"mTokenPwdRetryCount", "keyUID":"' + keyUID + '", "passWdType":' + passwdType + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.pwdRetryCount;
		}else{
			return -1;
		}
	};

	this.mTokenEncrypt = function(keyUID, method, paddingType, data)
	{
		var json = '{"function":"mTokenEncrypt", "keyUID":"' + keyUID + '", "method":' + method + ', "paddingType":' + paddingType + ', "inData":"' + data + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenDecrypt = function(keyUID,  method, paddingType, data)
	{
		var json = '{"function":"mTokenDecrypt", "keyUID":"' + keyUID + '", "method":' + method + ', "paddingType":' + paddingType + ', "inData":"' + data + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

}


/*******************************************************
 *
 * 使用此JS脚本之前请先仔细阅读mToKen K1帮助文档!
 *
 * @version	4.0
 * @date	2015/9/22
 * @explanation	mToKen K1 v3.0支持谷歌45版本及其他浏览器
 *
 **********************************************************/

function mTokenMgr(obj){
	this.obj = obj;

	var g_mTokenPlugin = null;

	this.LoadLibrary = function()
	{
		//浏览器判断
		if(isIe() ){	//IE
			if(!-[1,]){	//IE678
				g_mTokenPlugin = document.getElementById(obj);
			}else{	//IE9+
				if(!!window.ActiveXObject){
					g_mTokenPlugin = document.getElementById(obj);
					g_mTokenPlugin.setAttribute("type", "application/x-npmplugin");
				}else {
					g_mTokenPlugin = new K1AdminPlugin();
				}

			}
		}else {
			g_mTokenPlugin = new K1AdminPlugin();
		}

		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return 0;
	};

	this.K1Mgr_mTokenGetVersion = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGetVersion();
	};

	this.K1Mgr_mTokenFindDevice = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenFindDevice();
	};

	this.K1Mgr_mTokenGetLastError = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGetLastError();
	};

	this.K1Mgr_mTokenGetUID = function(keyIndex)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGetUID(keyIndex);
	};

	this.K1Mgr_mTokenOpen = function(keyUID, keyPassword)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenOpen(keyUID, keyPassword, 0);
	};

	this.K1Mgr_mTokenClose = function()
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenClose();
	};

	this.K1Mgr_mTokenChangePwd = function(keyUID,oldPassword, newPassword)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenChangePwd(keyUID, 0, oldPassword, newPassword);
	};

	this.K1Mgr_mTokenSetSeedKey = function(keyUID, seedKey)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenSetSeedKey(keyUID, seedKey);
	};

	this.K1Mgr_mTokenSetMainKey = function(keyUID, mainKey)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenSetMainKey(keyUID, mainKey);
	};

	this.K1Mgr_mTokenGenResetpwdResponse = function(mainKey, clientRequest, adminPwd, newUserPwd)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenGenResetpwdResponse(mainKey, clientRequest, adminPwd, newUserPwd);
	};

	this.K1Mgr_mTokenSetUserInfo = function(keyUID, openType, label, url, companyName, remarks, Tip)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenSetUserInfo(keyUID, openType, label, url, companyName, remarks, Tip);
	};

	this.K1Mgr_mTokenUnlockPwd = function(keyUID, adminPwd, userPwd)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenUnlockPwd(keyUID, adminPwd, userPwd);
	};

	this.K1Mgr_mTokenReadUserStorage = function(keyUID, offset, dataLength)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenReadUserStorage(keyUID, offset, dataLength);
	};

	this.K1Mgr_mTokenWriteUserStorage = function(keyUID, offset, writeData)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenWriteUserStorage(keyUID, offset, writeData);
	};


	this.K1Mgr_mTokenReadSecureStorageByAdmin = function(keyUID, offset, readLength)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenReadSecureStorageByAdmin(keyUID, offset, readLength);
	};

	this.K1Mgr_mTokenWriteSecureStorageByAdmin = function(keyUID, offset, writeData)
	{
		if(g_mTokenPlugin == null)
		{
			return -1;
		}

		return g_mTokenPlugin.mTokenWriteSecureStorageByAdmin(keyUID, offset, writeData);
	};

}




function K1AdminPlugin()
{

	var url = "http://127.0.0.1:51111/K1_Admin";

	var xmlhttp ;
	function AjaxIO(json) {
		if(xmlhttp == null) {
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		xmlhttp.open("POST", url, false);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send("json=" + json);
	}

	this.mTokenGetVersion = function()
	{
		var json = '{"function":"mTokenGetVersion"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenFindDevice = function()
	{
		var json = '{"function":"mTokenFindDevice"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.devCount;
		}else{
			return -2;
		}
	};

	this.mTokenGetLastError = function()
	{
		var json = '{"function":"mTokenGetLastError"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.errorCode;
		}else{
			return -2;
		}
	};

	this.mTokenGetUID = function(keyIndex)
	{
		var json = '{"function":"mTokenGetUID", "keyIndex":' + keyIndex + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenOpen = function(keyUID, keyPassword, type)
	{
		var json = '{"function":"mTokenOpen", "keyUID":"' + keyUID + '", "passWd":"' + keyPassword + '", "passWdType":' + type + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenClose = function()
	{
		var json = '{"function":"mTokenClose"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenChangePwd = function(keyUID, type, oldPassword, newPassword)
	{
		var json = '{"function":"mTokenChangePwd", "keyUID":"' + keyUID + '", "oldUpin":"' + oldPassword + '", "newUpin":"' + newPassword + '", "passWdType":' + type + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenSetSeedKey = function(keyUID, seedKey)
	{
		var json = '{"function":"mTokenSetSeedKey", "keyUID":"' + keyUID + '", "seedKey":"' + seedKey + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenSetMainKey = function(keyUID, mainKey)
	{
		var json = '{"function":"mTokenSetMainKey", "keyUID":"' + keyUID + '", "mainKey":"' + mainKey + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenGenResetpwdResponse = function(mainKey, clientRequest, adminPwd, newUserPwd)
	{
		var json = '{"function":"mTokenGenResetpwdResponse", "mainKey":"' + mainKey + '", "request":"' + clientRequest + '", "SuperPin":"' + adminPwd + '", "newUpin":"' + newUserPwd + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenSetUserInfo = function(keyUID, openType, label, url, companyName, remarks, Tip)
	{
		var json = '{"function":"mTokenSetUserInfo", "keyUID":"' + keyUID + '", "openType":' + openType + ', "label":"' + label + '", "Url":"' + url + '", "companyName":"' + companyName + '", "remarks":"' + remarks + '", "Tip":"' + Tip + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return -1;
		}
	};

	this.mTokenUnlockPwd = function(keyUID, adminPwd, userPwd)
	{
		var json = '{"function":"mTokenUnlockPwd", "keyUID":"' + keyUID + '", "UserPin":"' + userPwd + '", "SuperPin":"' + adminPwd + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return -1;
		}
	};

	this.mTokenReadSecureStorage = function(keyUID, offset, dataLength)
	{
		var json = '{"function":"mTokenReadSecureStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inDataLen":' + dataLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenWriteSecureStorage = function(keyUID, offset, writeData)
	{
		var json = '{"function":"mTokenWriteSecureStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inData":"' + writeData + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenReadUserStorage = function(keyUID, offset, dataLength)
	{
		var json = '{"function":"mTokenReadUserStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inDataLen":' + dataLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenWriteUserStorage = function(keyUID, offset, writeData)
	{
		var json = '{"function":"mTokenWriteUserStorage", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inData":"' + writeData + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};

	this.mTokenReadSecureStorageByAdmin = function(keyUID, offset, readLength)
	{
		var json = '{"function":"mTokenReadSecureStorageByAdmin", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inDataLen":' + readLength + '}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return "";
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.outData;
		}else{
			return "";
		}
	};

	this.mTokenWriteSecureStorageByAdmin = function(keyUID, offset, writeData)
	{
		var json = '{"function":"mTokenWriteSecureStorageByAdmin", "keyUID":"' + keyUID + '", "offset":' + offset + ', "inData":"' + writeData + '"}';
		try
		{
			AjaxIO(json);
		}
		catch (e)
		{
			return -3;
		}
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			var obj = eval("(" + xmlhttp.responseText + ")");
			return obj.rtn;
		}else{
			return 1;
		}
	};
}


/*******************************************************
 *
 * 函数名称：Base64encode()
 * 功    能：对数据进行Base64加密
 * 说	明：函数中将数据使用_utf8_encode()进行编码转换后再加密,保证数据完整
 *
 **********************************************************/

var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

function _Base64encode(input) {

	var output = "";
	var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
	var i = 0;

	input = _utf8_encode(input);

	while (i < input.length) {

		chr1 = input.charCodeAt(i++);
		chr2 = input.charCodeAt(i++);
		chr3 = input.charCodeAt(i++);

		enc1 = chr1 >> 2;
		enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
		enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
		enc4 = chr3 & 63;

		if (isNaN(chr2)) {
			enc3 = enc4 = 64;
		} else if (isNaN(chr3)) {
			enc4 = 64;
		}

		output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
	}
	return output;
}

/*******************************************************
 *
 * 函数名称：Base64decode()
 * 功    能：对数据进行Base64解密
 * 说	明：函数中将数据使用_utf8_decode()将解密后的数据编码,保证数据完整
 *
 **********************************************************/
function _Base64decode(input) {
	var output = "";
	var chr1, chr2, chr3;
	var enc1, enc2, enc3, enc4;
	var i = 0;

	input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

	while (i < input.length) {

		enc1 = this._keyStr.indexOf(input.charAt(i++));
		enc2 = this._keyStr.indexOf(input.charAt(i++));
		enc3 = this._keyStr.indexOf(input.charAt(i++));
		enc4 = this._keyStr.indexOf(input.charAt(i++));

		chr1 = (enc1 << 2) | (enc2 >> 4);
		chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
		chr3 = ((enc3 & 3) << 6) | enc4;

		output = output + String.fromCharCode(chr1);

		if (enc3 != 64) {
			output = output + String.fromCharCode(chr2);
		}
		if (enc4 != 64) {
			output = output + String.fromCharCode(chr3);
		}

	}

	output = _utf8_decode(output);

	return output;
}

/*******************************************************
 *
 * 函数名称：_utf8_encode()
 * 功    能：将数据进行utf8编码
 * 说	明：
 *
 **********************************************************/
function _utf8_encode(string) {
	string = string.replace(/\r\n/g, "\n");
	var utftext = "";

	for (var n = 0; n < string.length; n++) {

		var c = string.charCodeAt(n);

		if (c < 128) {
			utftext += String.fromCharCode(c);
		}
		else if ((c > 127) && (c < 2048)) {
			utftext += String.fromCharCode((c >> 6) | 192);
			utftext += String.fromCharCode((c & 63) | 128);
		}
		else {
			utftext += String.fromCharCode((c >> 12) | 224);
			utftext += String.fromCharCode(((c >> 6) & 63) | 128);
			utftext += String.fromCharCode((c & 63) | 128);
		}
	}
	return utftext;
}

/*******************************************************
 *
 * 函数名称：_utf8_decode()
 * 功    能：将数据进行utf8解码
 * 说	明：
 *
 **********************************************************/
function _utf8_decode(utftext) {
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;

	while (i < utftext.length) {

		c = utftext.charCodeAt(i);

		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		}
		else if ((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i + 1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		}
		else {
			c2 = utftext.charCodeAt(i + 1);
			c3 = utftext.charCodeAt(i + 2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}
	return string;
}


