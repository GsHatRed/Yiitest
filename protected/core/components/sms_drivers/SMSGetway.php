<?php

/**
 * SMSGetway class file.
 *
 * <pre>
 *      'sms' => array(
 *          'class' => 'core.components.TMobileSMS',
 *          'driverClass' => 'SMSGetway',
 *          'options'=> array(
 *              'protocol' => 'http',
 *              'url' => 'http://sdk2.zucp.net:8060/z_mdsmssend.aspx',      //接口地址
 *              'mobileParam' => 'mobile',                                  //手机号参数名
 *              'titleParam' => '',
 *              'contentParam' => 'content',                                //短信内容参数名
 *              'timeParam' => 'time',                                      //发送时间参参数名
 *              'charset' => 'gbk',                                         //编码支持
 *              'prefix' => '',                                             //前缀
 *              'affix' => '',                                              //后缀
 *              'params' => array(                                          //其他扩展参数
 *                  'sn'=> '',
 *                  'pwd'=> '',
 *              ),
 *          ),
 *      )
 * </pre>
 * <pre>
 *      'sms' => array(
 *          'class' => 'core.components.TMobileSMS',
 *          'driverClass' => 'SMSGetway',
 *          'options'=> array(
 *              'protocol' => 'soap',
 *              'url' => '',      //接口地址
 *              'mobileParam' => '',                                 //手机号参数名
 *              'titleParam' => '',
 *              'contentParam' => '',                                //短信内容参数名                                  //发送时间参参数名
 *              'charset' => 'utf-8',                                //编码支持
 *              'params' => array(                                   //其他扩展参数
 *              ),
 *          ),
 *      )
 * </pre>
 *
 *
 * @author lx <lx@tongda2000.com>
 */
define(PROTOCAL_HTTP, 'http');
define(PROTOCAL_SOAP, 'soap');

class SMSGetway extends CComponent implements ISMS {

    /**
     * @var string 接口地址
     */
    public $url;

    /**
     * @var string 接口类型
     */
    public $protocol;

    /**
     * @var string 手机号参数名
     */
    public $mobileParam;

    /**
     * @var string 手机号分割符号
     */
    public $mobileDelimiter = ',';
    public $titleParam = NULL;

    /**
     * @var string 短信内容参数名
     */
    public $contentParam = NULL;

    /**
     * @var string 发送时间参参数名
     */
    public $timeParam = NULL;

    /**
     * @var string 前缀
     */
    public $prefix = '';

    /**
     * @var string 后缀
     */
    public $affix = '';

    /**
     * @var array 其他扩展参数
     */
    public $params;

    /**
     * @var string 编码支持
     */
    public $charset = NULL;

    /**
     * 构造函数实例化网关配置模型，并将AR对象中相关配置参数赋值给对应的成员变量
     * @author Jmb <jmb@tongda2000.com>
     */
    public function __construct() {
        //查找正在启用的网关配置
        $gateway_result = GateWayConfig::model()->find(array(
            'condition' => 'is_activate=:activate',
            'params' => array(":activate" => 1)));

        //如果存在正在启用的网关配置，则把相关配置参数赋值给相应的网关参数
        if (!empty($gateway_result)) {
            $this->url = $gateway_result->gateway_url;
            $this->protocol = $gateway_result->protocol;
            $this->mobileParam = $gateway_result->mobile_param;
            $this->mobileDelimiter = $gateway_result->mobile_delimiter;
            $this->titleParam = $gateway_result->title_param;
            $this->contentParam = $gateway_result->content_param;
            $this->timeParam = $gateway_result->time_param;
            $this->charset = $gateway_result->charset;
            $this->prefix = $gateway_result->prefix;
            $this->affix = $gateway_result->affix;
            $this->params = unserialize($gateway_result->other_params);
        }
    }

    public function send($mobile = array(), $content = '', $time = NULL) {    
        if (trim($this->url) == '' || empty($this->mobileParam) || empty($this->contentParam)) {
            throw new Exception('短信网关参数配置有误！');
        }

        if (empty($this->mobileDelimiter)) {
            $this->mobileDelimiter = ',';
        }

        if (is_array($mobile)) {
            $strMobile = implode(',', array_unique($mobile));
        } elseif (is_string($mobile)) {
            $strMobile = $mobile;
        }

        if (trim($strMobile) == '') {
            throw new Exception('发送号码为空！');
        }

        $smsGrpstats = isset(Yii::app()->params['sms_grpstats']) ? Yii::app()->params['sms_grpstats'] : 50;
        $groupMobile = array_chunk(explode(',', trim($strMobile)), $smsGrpstats);
        $content = $this->prefix . $content . $this->affix;
        if (isset($this->charset)) {
            $content = TUtil::iconv($content, 'utf-8', $this->charset);
        }
        $this->params[$this->contentParam] = $content;
        if ($this->timeParam !== NULL) {
            $this->params[$this->timeParam] = is_numeric($time) ? $time : time();
        }

        if (!empty($groupMobile)) {
            foreach ($groupMobile as $key => $mobiles) {
                $this->params[$this->mobileParam] = implode($this->mobileDelimiter, array_unique($mobiles));
                if ($this->protocol == PROTOCAL_HTTP) {
                    $result[$key] = $this->_httpSend();
                } else if ($this->protocol == PROTOCAL_SOAP) {
                    $result[$key] = $this->_soapSend();
                }
            }
            return $result;
        }
    }

    private function _httpSend() {
        $this->params['ext']=1;
        $this->params['stime']='';
        $this->params['rrid']='';
        
        $ch = Yii::createComponent('core.components.TCurl');
        return $ch->get($this->url, $this->params);
    }

    /*
      private function _soapSend() {
      $version = $this->params['version'];
      unset($this->params['version']);
      $method = $this->params['method'];
      unset($this->params['method']);
      $xmlns = $this->params['xmlns'];
      unset($this->params['xmlns']);

      if($method == '') {
      return -2;
      }
      $body = "<".$method." xmlns=\"".$xmlns."\">\n\r";
      foreach($this->params as $key => $value){
      $body .= "<".$key.">".$value."</".$key.">\n\r";
      }
      $body .= "</".$method.">";
      $xmlData = <<<EOT
      <?xml version="1.0" encoding="utf-8"?>
      <soap{$version}:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
      <soap{$version}:Body>
      {$body}
      </soap{$version}:Body>
      </soap{$version}:Envelope>
      EOT;
      $ch = Yii::createComponent('core.components.TCurl');
      $ch->setHeaders('Content-type: application/soap+xml');
      CVarDumper::dump($xmlData);exit;
      return $ch->post($this->url, $xmlData);
      }
     */

    private function _soapSend() {
        $method = $this->params['method'];
        unset($this->params['method']);
        if ($method == '') {
            return -2;
        }
        $client = @ new SoapClient($this->url);
        return $client->$method($this->params);
    }

    public function getErrMsg($errNo) {
        if ($this->protocol == PROTOCAL_HTTP) {
            return getHttpErrMsg($errNo);
        } else {
            return getSoapErrMsg($errNo);
        }
    }

    public function getSoapErrMsg($errNo) {
        
    }

    public function getHttpErrMsg($errNo) {
        $msg = '';
        switch ($errNo) {
            case 1:
                $msg = '没有需要取得的数据';
                break;
            case -1:
                $msg = '重复注册';
                break;
            case -2:
                $msg = '帐号/密码不正确';
                break;
            case -4:
                $msg = '余额不足';
                break;
            case -5:
                $msg = '数据格式错误';
                break;
            case -6:
                $msg = '参数有误';
                break;
            case -7:
                $msg = '权限受限';
                break;
            case -8:
                $msg = '流量控制错误';
                break;
            case -9:
                $msg = '扩展码权限错误';
                break;
            case -10:
                $msg = '内容长度长';
                break;
            case -11:
                $msg = '内部数据库错误';
                break;
            case -12:
                $msg = '序列号状态错误';
                break;
            case -13:
                $msg = '没有提交增值内容';
                break;
            case -14:
                $msg = '服务器写文件失败';
                break;
            case -15:
                $msg = '文件内容base64编码错误';
                break;
            case -16:
                $msg = '返回报告库参数错误';
                break;
            case -17:
                $msg = '没有权限';
                break;
            case -18:
                $msg = '上次提交没有等待返回不能继续提交';
                break;
            case -19:
                $msg = '禁止同时使用多个接口地址';
                break;
            case -20:
                $msg = '相同手机号，相同内容重复提交';
                break;
            case -21:
                $msg = 'IP鉴权失败';
                break;
        }
        return $msg;
    }

}
