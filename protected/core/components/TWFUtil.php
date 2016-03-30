<?php
/**
 * TWFUtil class
 *
 * @author FL
 */
class TWFUtil {
    
    public static function getTaskPriv($wfid, $taskId){
        
        $model = WfDfWorkflow::model()->findByPk($wfid);
        
        $priv = $model->getTaskPriv($taskId);
        
        $result = array(
            'user_id' => TUtil::texplode($priv['user']['id']),
            'org_id' => TUtil::texplode($priv['dept']['id']),
            'role_id' => TUtil::texplode($priv['role']['id']),
            'position_id' => TUtil::texplode($priv['position']['id']),
        );
        return $result;
    }
    
    public static function getTaskFilter($wfid, $taskId){
        
        $model = WfDfWorkflow::model()->findByPk($wfid);
        
        $priv = $model->getTaskPriv($taskId);
        return array('type' => $priv['filter']['type'],'node' => $priv['filter']['params']['nodeId']);
    }
    
    public static function getTaskLineFilter($wfid, $nodeId, $curNodeId) {
        $model = WfDfWorkflow::model()->findByPk($wfid);
        
        $rule = $model->getLineRule($nodeId, $curNodeId)[0];
        return array('type' => $rule['type'],'filter' => $rule['filter']);   
    }

    /**
     * @param $wf 流程ID
     * @param $wft 转交节点ID
     * @param $wfnode 当前办理节点ID
     * @return array
     */
    public static function getTaskHandler($wf, $wft, $wfnode = '',$proc = ''){
        $priv = self::getTaskPriv($wf, $wft);
        $result = User::getUsersByScope(array(
            'user'=> implode(',', $priv['user_id']),
            'org' => implode(',', $priv['org_id']),
            'role' => implode(',', $priv['role_id']),
            'position' => implode(',', $priv['position_id'])
        ), User::UCT_PLAIN_ARRAY);
        if(!empty($result)) {
            foreach($result as $key => $user) {
                if(!$user['is_enabled']) {
                    unset($result[$key]);
                }
            }
        }
        $currOrg = Yii::app()->user->org['all'];
        $curUserId = Yii::app()->user->id;
        if($wft == $wfnode) {//内循环节点，过滤当前节点办理人
            $filterModels = WfRtTask::model()->findAll(array('select' => array('task_user'), 'condition' => TUtil::qc('task_id') . '=:task_id AND ' . TUtil::qc('process_id') . '=:process_id AND ' . TUtil::qc('status') . '=1' , 'params' => array(':task_id' =>$wfnode, ':process_id' => $proc)));
            if(!empty($filterModels)) {
                foreach($filterModels as $filterModel) {
                    $filterUsers [] = $filterModel['task_user'];
                }
                foreach($result as $key => $item){
                    if(TUtil::in_array($item['id'], $filterUsers)){
                        unset($result[$key]);
                    }
                }
            }
        }
        $taskFilter = self::getTaskLineFilter($wf, $wft, $wfnode);
        $isCurrOrg = $taskFilter['type'] == '1' ? true : false;
        $excludeSelf = $taskFilter['filter'] == '1' ? true : false;
        if($isCurrOrg == true) {
            foreach($result as $key => $item){
                if(!TUtil::in_array($item['org']['all'], $currOrg)){
                    unset($result[$key]);
                }
            }
        }
        if($excludeSelf == true) {
            foreach($result as $key => $item){
                if($item['id'] == $curUserId){
                    unset($result[$key]);
                }
            }
        }
        if($taskFilter['type'] == 4 && Yii::app()->params['project'] == 'cqhbj') {//只显示历史闭环人
            $models = WfRtTask::model()->findAll(array('select' => array('from_node_id', 'create_user'), 'condition' => TUtil::qc('task_id') . '=:task_id AND ' . TUtil::qc('task_user') . '=:task_user AND ' . TUtil::qc('process_id') . '=:process_id' , 'params' => array(':task_id' =>$wfnode, ':task_user'=>Yii::app()->user->id, ':process_id' => $proc)));
            if($models) {
                foreach($models as $model) {
                    if($model['from_node_id'] == $wft) {
                        $users[] = $model['create_user'];
                    }
                }
            }
            if(!empty($users)) {
                foreach($result as $key => $item){
                    if(!TUtil::in_array($item['id'], $users)){
                        unset($result[$key]);
                    }
                }
            }
        }
        return $result;
    }

}
