<?php

Yii::import('core.components.algorithms.IAlgorithm');
/**
 * mcrypt库提供的加解密算法封装类
 * @author lx
 * @copyright Copyright (c) 2012 Tongda Tech, Inc.
 * @version v1.0
 */
class AlgStandard implements IAlgorithm
{

    /**
     * Version
     *
     * @var string
     * @access private
     */
    private $_ver = "1.0.120424";
      

    /**
     * Mcrypt td resource
     *
     * @var resource
     * @access private
     */
    private $_td = null;

    /**
     * Initialization vector
     *
     * @var string
     * @access private
     */
    private $_iv = null;  

    /**
     * key size of the cipher
     *
     * @var int
     * @access private
     */    
    private $_key_size = null;
      
    /**
     * Constructor
     *
     * @param string $cipher
     * @access public
     */
    public function __construct($cipher)
    {
        switch($cipher) 
        { 
            case "DES-64": 
                $this->_key_size = 64;
                $this->_td = mcrypt_module_open('des', '', 'ecb', ''); 
                break;   
            case "BLOWFISH-128": 
                $this->_key_size = 128;
                $this->_td = mcrypt_module_open('blowfish', '', 'ecb', ''); 
                break;  
            case "BLOWFISH-256": 
                $this->_key_size = 256;
                $this->_td = mcrypt_module_open('blowfish', '', 'ecb', ''); 
                break;   
            case "RC2-128": 
                $this->_key_size = 128;
                $this->_td = mcrypt_module_open('rc2', '', 'ecb', ''); 
                break; 
            case "RC2-256": 
                $this->_key_size = 256;
                $this->_td = mcrypt_module_open('rc2', '', 'ecb', ''); 
                break;       
        }
        $key_size = ($this->_key_size && $this->_key_size < mcrypt_enc_get_iv_size($this->_td)) ? $this->_key_size : mcrypt_enc_get_iv_size($this->_td);
        $this->_iv = mcrypt_create_iv($key_size, MCRYPT_RAND); 
    }

    /**
     * Set the key.
     * @param string $key
     */    
    function setKey($key)
    {
        $key = substr($key, 0, mcrypt_enc_get_key_size($this->_td)); 
        mcrypt_generic_init($this->_td, $key, $this->_iv); 
    }
    
    
    /**
     * Encrypt the data.
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        $encrypted_data = mcrypt_generic($this->_td, $data);
        return $encrypted_data;
    }
    
    
    /**
     * Decrypt the data.
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        $decrypted_data = mdecrypt_generic($this->_td, $data);
        return $decrypted_data;
    }

    /**
     * Get the version of algorithm.
     * @return string
     */
    public function getVersion()
    {
        return $this->_ver;
    }
}