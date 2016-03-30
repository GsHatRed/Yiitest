<?php

/**
 * TokenizerSCWS class file.
 *
 * @author lx <lx@tongda2000.com>
 */
class TokenizerSCWS extends CComponent implements ITokenizer {

    private $_instance = NULL;
    public $charset = 'utf8';

    public function __construct() {
        if (extension_loaded('scws')) {
            $this->_instance = scws_new();
            $this->_instance->set_charset($this->charset);
            $this->_instance->set_ignore(true);
        } else if(YII_DEBUG){
            throw new CException('PHP扩展scws未安装',500);
        }
    }
    
    public function __destruct() {
        if(is_object($this->_instance)) {
            $this->_instance->close();
        }
    }

    public function getTokens($attr = '') {
        if(is_object($this->_instance)) {
            return $this->_instance->get_words($attr);
        }
    }

    public function getTops($limit = 5, $filter = '') {
        if(is_object($this->_instance)) {
            return $this->_instance->get_tops($limit, $filter);
        }
    }
    
    public function getTopWords($limit = 5, $filter = '') {
        $tops = $this->getTops($limit, $filter);
        $word = '';
        if(!empty($tops)) {
            foreach($tops as $k=>$v){
                $word .= $v['word'].",";
            }
        }
        return rtrim($word,',');
    }

    public function setText($text) {
        if(is_object($this->_instance)) {
            $this->_instance->send_text($text);
        }
    }
    
    public function __toString() {
        if(extension_loaded('scws')) {
            $functions = get_extension_funcs('scws');
            return implode(',', $functions);
        }
        
    }

}

?>
