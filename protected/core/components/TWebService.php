<?php
/**
 * Created by PhpStorm.
 * User: hf
 * Date: 2015/7/14
 * Time: 13:12
 */
class TWebService {
    public $client = null;
    public function __construct($wsdl,$options = array()) {
        try{
            if(!isset($options['cache_wsdl'])) {
                $options['cache_wsdl'] = WSDL_CACHE_NONE;
            }
            $soapClient = new SoapClient($wsdl,$options);
            $this->client = $soapClient;
        } catch(SoapFault $e) {
            Yii::trace($e->getMessage());
        }
    }
}