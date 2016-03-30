<?php
/**
 * Description of SyncCms
 *
 * @author F.L
 */
class OrgSync extends CComponent implements IOrgSync {
    
    public $serviceUrl;
    public $client;
    public $usingFlag = false;
    public $successLog = false;
    
    public function __construct() {
        $this->open();
    }
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
            Yii::app()->core->log($exc->getMessage(), 'orgsync', 1);
            return false;
        }
        return $result;
    }
    
    public function toJSON($operation, $orgData, $userData = array()){
        if(!is_array($orgData) && !($orgData instanceof Org)){
            return false;
        }
        $jsonData = array(
            'operation' => $operation,
            'orgList' => array(),
        );
        if(is_array($orgData)){
            foreach($orgData as $org){
                if($org instanceof Org){
                    $jsonData['orgList'][] = array(
                        'orgUuid' => $org->guid,
                        'parentUuid' => Org::getUUIDByID($org->org_parent),
                        'name' => $org->org_name,
                    );
                }
            }
        } else {
            $jsonData['orgList'][] = array(
                'orgUuid' => $orgData->guid,
                'parentUuid' => Org::getUUIDByID($orgData->org_parent),
                'name' => $orgData->org_name,
            );
        }

        if(is_array($userData)){
            foreach($userData as $user){
                if($user instanceof User){
                    $org = Org::model()->findByPk(UserOrg::getUserMainOrg($user->id));
                    $jsonData['userList'][] = array(
                        'uuid' => $user->uuid,
                        'username' => $user->user_id,
                        'fullname' => $user->user_name,
                        'password' => '',
                        'orgUuid' => $org->guid,
                    );
                }
            }
        } else {
            $org = Org::model()->findByPk(UserOrg::getUserMainOrg($userData->id));
            $jsonData['userList'][] = array(
                'uuid' => $userData->uuid,
                'username' => $userData->user_id,
                'fullname' => $userData->user_name,
                'password' => '',
                'orgUuid' => $org->guid,
            );
        }
        $jsonData['token'] = md5($operation) . time();
        return CJSON::encode($jsonData);
    }
    

    public function add($data) {
        if($this->open()){
            $jsonString = $this->toJSON('AddOrgExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('添加组织成功！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
                return true;
            } else {
                Yii::app()->core->log('添加组织失败！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
            }
        }
        return false;
    }

    public function addUserToOrg($orgId, $userId) {
        if(is_array($orgId)){
            $orgData = array();
            foreach($orgId as $org){
                $orgData[] = Org::model()->findByPk($org);
            }
        } else {
            $orgData = Org::model()->findByPk($orgId);
        }
        
        if(is_array($userId)){
            $userData = array();
            foreach($userId as $user){
                $userData[] = User::model()->findByPk($user);
            }
        } else {
            $userData = User::model()->findByPk($userId);
        }
        if($this->open()){
            $jsonString = $this->toJSON('AddUserToOrg', $orgData, $userData);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('添加用户到组织成功！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
                return true;
            } else {
                Yii::app()->core->log('添加用户到组织失败！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
            }
        }
        return false;
    }

    public function deleteOrgPermanent($data) {
        if($this->open()){
            $jsonString = $this->toJSON('DeleteOrgPermanentExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('删除组织成功！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
                return true;
            } else {
                Yii::app()->core->log('删除组织失败！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
            }
        }
        return false;
    }

    public function markDeleteOrg($data) {
        $this->deleteOrgPermanent($data);
    }

    public function removeUserFromOrg($orgId, $userId) {
        if(is_array($orgId)){
            $orgData = array();
            foreach($orgId as $org){
                $orgData[] = Org::model()->findByPk($org);
            }
        } else {
            $orgData = Org::model()->findByPk($orgId);
        }
        
        if(is_array($userId)){
            $userData = array();
            foreach($userId as $user){
                $userData[] = User::model()->findByPk($user);
            }
        } else {
            $userData = User::model()->findByPk($userId);
        }
        if($this->open()){
            $jsonString = $this->toJSON('RemoveUserFromOrg', $orgData, $userData);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('从组织删除用户成功！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
                return true;
            } else {
                Yii::app()->core->log('从组织删除用户失败！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
            }
        }
        return false;
    }

    public function updateAllOrg($data) {
        
    }

    public function updateOrg($data) {
        if($this->open()){
            $jsonString = $this->toJSON('UpdateOrgExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('更新组织成功！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
                return true;
            } else {
                Yii::app()->core->log('更新组织失败！||data='.$jsonString.'||result='.  json_encode($result), 'orgsync', 1);
            }
        }
        return false;
    }

}
