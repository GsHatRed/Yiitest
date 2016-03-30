<?php
/**
 * Description of SyncCms
 *
 * @author F.L
 */
class UserSync extends CComponent implements IUserSync {
    
    public $serviceUrl;
    public $client;
    public $usingFlag = false;
    public $successLog = false;
    public $encrypt = true;
    
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
            Yii::app()->core->log($exc->getMessage(), 'usersync', 1);
            return false;
        }
        return $result;
    }
    
    public function toJSON($operation, $data){
        if(!is_array($data) && !($data instanceof User)){
            return false;
        }
        $jsonData = array(
            'operation' => $operation,
            'userList' => array(),
            'token' => md5($operation) . time(),
        );
        if(is_array($data)){
            foreach($data as $user){
                if($user instanceof User){
                    $org = Org::model()->findByPk(UserOrg::getUserMainOrg($user->id));
                    $password = $user->new_password ? $user->new_password : $user->password;
                    if($this->encrypt)
                        $password = base64_encode($password);
                    $jsonData['userList'][] = array(
                        'uuid' => $user->uuid,
                        'username' => $user->user_id,
                        'fullname' => $user->user_name,
                        'password' => $password,
                        'orgUuid' => $org->guid,
                    );
                }
            }
        } else {
            $org = Org::model()->findByPk(UserOrg::getUserMainOrg($data->id));
            $password = $data->new_password ? $data->new_password : $data->password;
            if($this->encrypt)
                $password = base64_encode($password);
            $jsonData['userList'][] = array(
                'uuid' => $data->uuid,
                'username' => $data->user_id,
                'fullname' => $data->user_name,
                'password' => $password,
                'orgUuid' => $org->guid,
            );
        }
        return CJSON::encode($jsonData);
    }
    
    public function add($data) {
        if($this->open()){
            $jsonString = $this->toJSON('AddUserExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('添加用户成功！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
                return true;
            } else {
                Yii::app()->core->log('添加用户失败！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }

    public function deletePermanent($data) {
        if($this->open()){
            $jsonString = $this->toJSON('DeleteUserPermanentExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('永久删除用户成功！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
                return true;
            } else {
                Yii::app()->core->log('永久删除用户失败！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }

    public function markDelete($data) {
        if($this->open()){
            $jsonString = $this->toJSON('MarkDeleteUserExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('标记删除用户成功！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
                return true;
            } else {
                Yii::app()->core->log('标记删除用户失败！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }

    public function resumeMarked($data) {
        if($this->open()){
            $jsonString = $this->toJSON('ResumeMarkedUserExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('恢复标记删除用户成功！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
                return true;
            } else {
                Yii::app()->core->log('恢复标记删除用户失败！||data='.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }

    public function updateAllUser($data) {
        
    }

    public function update($data) {
        if($this->open()){
            $jsonString = $this->toJSON('UpdateUserExtended', $data);
            $result = $this->call($jsonString);

            if($result && $result->out == 'true'){
                Yii::app()->core->log('更新用户同步失败！||data='.$jsonString.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
                return true;
            } else {
                Yii::app()->core->log('更新用户同步失败！||data='.$jsonString.$jsonString.'||result='.  json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }
    
    public function updatePassword($data){
        if($this->open()){
            $jsonString = $this->toJSON('UpdateUserPassword', $data);
            $result = $this->call($jsonString);
            if($result && $result->out == 'true'){
                if($this->successLog)
                    Yii::app()->core->log('用户密码同步成功！||data='.$jsonString.'||result='.json_encode($result), 'usersync');
                return true;
            } else {
                Yii::app()->core->log('用户密码同步失败！||data='.$jsonString.'||result='.json_encode($result), 'usersync', 1);
            }
        }
        return false;
    }

}
