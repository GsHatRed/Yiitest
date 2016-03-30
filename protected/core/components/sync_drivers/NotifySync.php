<?php
/**
 * Description of SyncCms
 *
 * @author F.L
 */
class NotifySync extends CComponent {
    
    public $serviceUrl;
    public $client;
    public $usingFlag = false;
    public $successLog = false;
    
    public function open(){
        if(!$this->usingFlag)
            return false;
        try {
            $client = new SoapClient($this->serviceUrl);
        } catch (Exception $ex) {
            return false;
        }
        $this->client = $client;
        return true;
    }
    
    public function call($jsonString){
        try {
            $result = $this->client->wsTransIcssPsg(array(
                'in0' => $jsonString
            ));
        } catch (Exception $exc) {
            Yii::app()->core->log($exc->getMessage(), 'notifysync', 1);
            return false;
        }
        return $result;
    }
    
    public function toJSON($operation, $data){
        $jsonData = array(
            'operation' => $operation,
            'token' => md5($operation) . time(),
        );
        if($operation == 'NotifyGetUnreadCount'){
            $jsonData['uuid'] = strval($data);
        } else {
            $jsonData['num'] = strval($data);
        }
        return CJSON::encode($jsonData);
    }
    
    /**
     * 
     * Array(
     *   [0] => Array(
     *     [title] => 关于发布《XXXX》的通告,
     *     [infoid] => 1067,
     *     [publishname] => XXX,
     *     [publishtime] => 1406822400000,
     *     [channelid] => 877
     *   )
     * )
     * 
     * @param type $num
     * @return type
     */
    public function detailAnnouncement($num = 5) {
        if($this->open()){
            $jsonString = $this->toJSON('DetailAnnouncement', $num);
            $result = $this->call($jsonString);
            if($result && $result->out != 'false'){
                $data = CJSON::decode($result->out);
                return $data;
            }
        }
        return array();
    }
    
    public function getUnreadCount($userId) {
        if($this->open()){
            $jsonString = $this->toJSON('NotifyGetUnreadCount', $userId);
            $result = $this->call($jsonString);
            if($result && $result->out != 'false'){
                return intval($result->out);
            }
        }
        return 0;
    }
}
