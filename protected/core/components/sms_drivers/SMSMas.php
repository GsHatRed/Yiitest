<?php
/**
 * SMSModem class file.
 * 嘉讯移动信息机
 * 
 * @author lx <lx@tongda2000.com>
 */
class SMSMas extends CComponent implements ISMS
{   
    /**
     * @var string 前缀
     */
    public $prefix = '';

    /**
     * @var string 后缀
     */
    public $affix = '';
    /**
     * @var string 编码支持
     */
    public $charset = NULL;
    
    
    public function send($mobile=array(), $content='', $time='') {
        if(!extension_loaded('mas')) {
            throw new CException('PHP扩展未加载');
        }
        
        if (is_array($mobile)) {
            $strMobile = implode(',', array_unique($mobile));
        } elseif (is_string($mobile)) {
            $strMobile = $mobile;
        }

        if (trim($strMobile) == '') {
            throw new CException('发送号码为空！');
        }
        
        $content = $this->prefix . $content . $this->affix;
        if (isset($this->charset)) {
            $content = TUtil::iconv($content, 'utf-8', $this->charset);
        }
               
        return mas_send($strMobile, $content, $time);       
    }
    
    public function getErrMsg()
    {
        return mas_get_error();
    }
}

