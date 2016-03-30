<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class TUserIdentity extends CUserIdentity {

    private $_id = null;
    private $_authtype;
    private $_logintype;
    private $_params = array();
    private $_user = null;

    const ERROR_AUTHTYPE_INVALID = 3;
    const ERROR_STATUS_NOTEXSIT = 4;
    const ERROR_STATUS_NOTACTIVE = 5;
    const ERROR_DATABASE = 6;
    const ERROR_RETRY = 7;
    const ERROR_CERTSN = 8;
    const ERROR_USBKEY = 9;
    const ERROR_USBKEY_SN = 10;
    const ERROR_USBKEY_DATA = 11;
    const ERROR_SECUREKEY = 12;
    const ERROR_SECUREKEY_INFO = 13;
    const ERROR_LDAP_CONNECTION = 14;
    const ERROR_LDAP_BIND = 15;
    const ERROR_OVER_EXPIRE = 16;
    const AUTH_TYPE_DEFAULT = 0;
    const AUTH_TYPE_CA = 1;
    const AUTH_TYPE_USBKEY = 2;
    const AUTH_TYPE_SECUREKEY = 3;

    /**
     * Constructor.
     * @param int $authtype authtype
     * @param string $username username
     * @param string $password password
     * @param array $params 其他参数
     */
    public function __construct($authtype = self::AUTH_TYPE_DEFAULT, $username, $password, $params = array(), $logintype = "teacher") {
        $this->_authtype = $authtype;
        $this->_params = $params;
        $this->_logintype = $logintype;
        parent::__construct($username, $password);
    }

    /**
     * get Uid
     * @return integer
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * 获取当前用户数据
     * @return object
     */
    public function getUser() {
        if ($this->_logintype == "student") {
            $userModel = EduStudents::model()->findByPk($this->_id);
        } else if ($this->_logintype == "parent") {
            $userModel = EduParents::model()->findByPk($this->_id);
        } else {
            $userModel = User::model()->with('userProfile')->findByPk($this->_id);
        }
        return $this->_user == null ? $userModel : $this->_user;
    }

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate() {
        $this->errorCode = self::ERROR_NONE;
        if (!isset($this->_authtype) || !in_array($this->_authtype, array(self::AUTH_TYPE_DEFAULT, self::AUTH_TYPE_CA, self::AUTH_TYPE_SECUREKEY, self::AUTH_TYPE_USBKEY)))
            $this->errorCode = self::ERROR_AUTHTYPE_INVALID;
        else if (!$this->username && $_POST['login_type'] != self::AUTH_TYPE_CA)
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        else {
            if ($_POST['login_type'] == self::AUTH_TYPE_CA) {//证书登录
                if ($_POST['signedtext']) {
                    $signedtext = $_POST['signedtext'];
                    $webservice = Yii::app()->params['webservice'];
                    $soapClient = new TWebService(null, array('location' => $webservice['location'], 'uri' => $webservice['uri']));
                    $client = $soapClient->client;
                    if ($client->__soapCall('checkSNCAPKCS7Certificate', array('Signdata' => $signedtext)) == 'true') {
                        $certSn = $client->__soapCall('getSNCAOID', array('Signdata' => $signedtext, 'PKCS7' => 'PKCS7'));
                        $user = User::model()->with('userProfile')->find(TUtil::qc('cert_sn') . '=:cert_sn AND ' . TUtil::qc('cert_sn') . '!=""', array(':cert_sn' => $certSn));
                        if ($user == null) {
                            $this->errorCode = self::ERROR_STATUS_NOTEXSIT;
                        } else {
                            $this->_id = $user->id;
                            $this->_user = $user;
                        }
                    } else {
                        $this->errorCode = self::ERROR_CERTSN;
                    }
                } else {
                    $this->errorCode = self::ERROR_CERTSN;
                }
            } else {

                if ($this->_logintype == "teacher") {
                    $user = User::model()->with('userProfile')->find(TUtil::qc('user_id') . "= :user_id or " . TUtil::qc('byname') . "=:byname", array(':user_id' => $this->username, ':byname' => $this->username));
                    if ($user !== null) {
                        $this->_id = $user->id;
                        $this->_user = $user;
                        //数据异常
                        if (!isset($user->userProfile)) {
                            $this->errorCode = self::ERROR_DATABASE;
                        } else if ($user->not_login == 1 || $user->limit_login == 1 || $user->is_delete == 1) { //禁止登录
                            $this->errorCode = self::ERROR_STATUS_NOTACTIVE;
                        } else {
                            //密码连续错误后禁止登录
                            $retryParams = SysParams::getParams('login_retry_flag,login_retry_limit,login_retry_interval');
                            if ($retryParams['login_retry_flag'] == 1) {
                                $count = SysLog::model()->count(
                                        TUtil::qc('log_type') . "=:log_type and " . TUtil::qc('level') . "=:log_level and " . TUtil::qc('user_id') . "=:user_id and " . TUtil::qc('log_ip') . "=:log_ip and " . TUtil::qc('log_time') . ">:check_time", array(
                                    ':log_type' => SysLog::TYPE_LOGIN,
                                    ':log_level' => SysLog::LEVEL_ERROR,
                                    ':user_id' => $user->id,
                                    ':log_ip' => Yii::app()->core->getRealIp(),
                                    ':check_time' => time() - $retryParams['login_retry_interval'] * 60,
                                        )
                                );
                                if ($count >= $retryParams['login_retry_limit']) {
                                    $this->errorCode = self::ERROR_RETRY;
                                    return !$this->errorCode;
                                }
                            }
                            //验证用户名密码
                            if (!($this->_authtype == self::AUTH_TYPE_USBKEY && !SysParams::getParams('login_key_auto') && $this->_params['client'] != 'mobile') && !$this->_params['unified_auth'] && !$this->_params['sso']) {
                                if (crypt($this->password, $user->password) !== $user->password) {
                                    $this->errorCode = self::ERROR_PASSWORD_INVALID;
                                    Yii::app()->core->log('登陆失败，密码错误' . TUtil::maskstr($this->password, 2, 1), SysLog::TYPE_LOGIN, SysLog::LEVEL_ERROR, '', $user);
                                    return !$this->errorCode;
                                }
                            }

                            //辅助认证方式 排除移动端登录验证
                            if($this->_params['client'] != 'mobile') {
                                if ($this->_authtype == self::AUTH_TYPE_CA && $user->cert_sn != $this->_params['certsn'] && $_POST['login_type'] == 1) {
                                    $this->errorCode = self::ERROR_CERTSN;
                                } elseif ($this->_authtype == self::AUTH_TYPE_USBKEY) {
                                    if (Yii::app()->hasComponent('usbkeyIdentity') && is_callable(array(Yii::app()->usbkeyIdentity, 'authenticate'), true)) {
                                        if (false == Yii::app()->usbkeyIdentity->authenticate($user, $this->_params['secret'])) {
                                            $this->errorCode = self::ERROR_USBKEY;
                                        }
                                    } else {
                                        //龙脉K1系列mtoken
                                        if ($user->useing_key && $user->key_sn != '') {
                                            if ($user->key_sn != $this->_params['secret']) {
                                                $this->errorCode = self::ERROR_USBKEY;
                                            }
                                        } else {
                                            if (crypt($this->password, $user->password) !== $user->password) {
                                                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                                                Yii::app()->core->log('登陆失败，密码错误' . TUtil::maskstr($this->password, 2, 1), SysLog::TYPE_LOGIN, SysLog::LEVEL_ERROR, '', $user);
                                                return !$this->errorCode;
                                            }
                                        }
                                        //TODO 飞天1000nd系列usbkey登录
//                                    if ($user->key_sn != '') {
//                                        $secret = json_decode($this->_params['secret']);
//                                        if ($secret == null) {
//                                            $this->errorCode = self::ERROR_USBKEY;
//                                        } else {
//
//                                        }
//                                    }
                                    }
                                } elseif ($this->_authtype == self::AUTH_TYPE_SECUREKEY) {
                                    //TODO 海月动态密码卡验证
                                }
                            }
                        }
                    } else {
                        $this->errorCode = self::ERROR_STATUS_NOTEXSIT;
                    }
                } else if (in_array($this->_logintype, array("student", "parent"))) {  //学生和家长登录
                    Yii::import("edustudent.models.*");
                    if ($this->_logintype == "student") {
                        $user = EduStudents::model()->find(TUtil::qc('student_id') . "= :student_id or " . TUtil::qc('login_name') . "=:login_name", array(':student_id' => $this->username, ':login_name' => $this->username));
                    } else {
                        $user = EduParents::model()->find(TUtil::qc('mobile_phone') . "= :mobile_phone or " . TUtil::qc('login_name') . "=:login_name", array(':mobile_phone' => $this->username, ':login_name' => $this->username));
                    }
                    if ($user !== null) {
                        $this->_id = $user->id;
                        $this->_user = $user;
                        if ($user->not_login == 1 || $user->is_graduta == 2) { //禁止登录
                            $this->errorCode = self::ERROR_STATUS_NOTACTIVE;
                        } else {
                            Yii::import('application.core.models.EduSysLog');
                            //密码连续错误后禁止登录
                            $retryParams = SysParams::getParams('login_retry_flag,login_retry_limit,login_retry_interval');
                            if ($retryParams['login_retry_flag'] == 1) {
                                $count = EduSysLog::model()->count(
                                        TUtil::qc('log_type') . "=:log_type and " . TUtil::qc('level') . "=:log_level and " . TUtil::qc('user_id') . "=:user_id and " . TUtil::qc('log_ip') . "=:log_ip and " . TUtil::qc('log_time') . ">:check_time and " . TUtil::qc('people_type') . "=:people_type", array(
                                    ':log_type' => SysLog::TYPE_LOGIN,
                                    ':log_level' => SysLog::LEVEL_ERROR,
                                    ':user_id' => $user->id,
                                    ':log_ip' => Yii::app()->core->getRealIp(),
                                    ':check_time' => time() - $retryParams['login_retry_interval'] * 60,
                                    ':people_type' => $this->_logintype == "student" ? 0 : 1,
                                        )
                                );
                                if ($count >= $retryParams['login_retry_limit']) {
                                    $this->errorCode = self::ERROR_RETRY;
                                    return !$this->errorCode;
                                }
                            }
                            if (crypt($this->password, $user->password) !== $user->password) {
                                $this->errorCode = self::ERROR_PASSWORD_INVALID;
                                Yii::app()->core->edulog('登陆失败，密码错误' . TUtil::maskstr($this->password, 2, 1), $user, $this->_logintype == "student" ? 0 : 1, SysLog::TYPE_LOGIN, SysLog::LEVEL_ERROR);
                                return !$this->errorCode;
                            }
                        }
                    } else {
                        $this->errorCode = self::ERROR_STATUS_NOTEXSIT;
                    }
                }
            }
        }
        $code = TTask::getSysPublicKey();
        if ($code !== false) {
            $installDate = TTask::installDate($code,trim($this->username)=="admin");
            if (!$installDate && (trim($this->username)!="admin")) {
                $this->errorCode = self::ERROR_OVER_EXPIRE;
            }
        }
        return !$this->errorCode;
    }

}
