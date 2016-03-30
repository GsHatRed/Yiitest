<?php

/**
 * TDbAuthManager class file.
 *
 * @author fl <fl@tongda2000.com>
 */
class TDbAuthManager extends CDbAuthManager {

    /**
     * Performs access check for the specified user.
     * @param string $itemName the name of the operation that need access check
     * @param mixed $userId the user ID. This should can be either an integer and a string representing
     * the unique identifier of a user. See {@link IWebUser::getId}.
     * @param array $params name-value pairs that would be passed to biz rules associated
     * with the tasks and roles assigned to the user.
     * Since version 1.1.11 a param with name 'userId' is added to this array, which holds the value of <code>$userId</code>.
     * @return boolean whether the operations can be performed by the user.
     */
    public function checkAccess($itemName, $userId, $params = array()) {
        $item = $this->getAuthItemByName($itemName);
        //从会话取用户的权限集合
        $userOperations = Yii::app()->user->operations;
        if(is_array($userOperations)) {
            return in_array($item->id, $userOperations);
        } else {
            $assignments = $this->getAuthAssignments($userId);
            return $this->checkAccessRecursive($item->id, $userId, $params, $assignments);
        }
    }

    /**
     * Performs access check for the specified user.
     * This method is internally called by {@link checkAccess}.
     * @param string $itemId the name of the operation that need access check
     * @param mixed $userId the user ID. This should can be either an integer and a string representing
     * the unique identifier of a user. See {@link IWebUser::getId}.
     * @param array $params name-value pairs that would be passed to biz rules associated
     * with the tasks and roles assigned to the user.
     * Since version 1.1.11 a param with name 'userId' is added to this array, which holds the value of <code>$userId</code>.
     * @param array $assignments the assignments to the specified user
     * @return boolean whether the operations can be performed by the user.
     * @since 1.1.3
     */
    protected function checkAccessRecursive($itemId, $userId, $params, $assignments) {
        if (($item = $this->getAuthItem($itemId)) === null)
            return false;
        Yii::trace('Checking permission "' . $item->getId() . '"', 'application.core.TDbAuthManager');
        if (!isset($params['userId']))
            $params['userId'] = $userId;
        if ($this->executeBizRule($item->getBizRule(), $params, $item->getData())) {
            if (in_array($itemId, $this->defaultRoles))
                return true;
            if (isset($assignments[$itemId])) {
                $assignment = $assignments[$itemId];
                if ($this->executeBizRule($assignment->getBizRule(), $params, $assignment->getData()))
                    return true;
            }
            $parents = $this->db->createCommand()
                    ->select('parent')
                    ->from($this->itemChildTable)
                    ->where(TUtil::qc('child').'=:id', array(':id' => $itemId))
                    ->queryColumn();
            foreach ($parents as $parent) {
                if ($this->checkAccessRecursive($parent, $userId, $params, $assignments))
                    return true;
            }
        }
        return false;
    }

    /**
     * Adds an item as a child of another item.
     * @param string $itemId the parent item name
     * @param string $childId the child item name
     * @return boolean whether the item is added successfully
     * @throws CException if either parent or child doesn't exist or if a loop has been detected.
     */
    public function addItemChild($itemId, $childId) {
        if ($itemId === $childId)
            throw new CException(Yii::t('yii', 'Cannot add "{id}" as a child of itself.', array('{id}' => $itemId)));

        $rows = $this->db->createCommand()
                ->select()
                ->from($this->itemTable)
                ->where(TUtil::qc('id').'=:id1 OR '.TUtil::qc('id').'=:id2', array(
                    ':id1' => $itemId,
                    ':id2' => $childId
                ))
                ->queryAll();

        if (count($rows) == 2) {
            if ($rows[0]['id'] === $itemId) {
                $parentType = $rows[0]['type'];
                $childType = $rows[1]['type'];
            } else {
                $childType = $rows[0]['type'];
                $parentType = $rows[1]['type'];
            }
            $this->checkItemChildType($parentType, $childType);
            if ($this->detectLoop($itemId, $childId))
                throw new CException(Yii::t('yii', 'Cannot add "{child}" as a child of "{id}". A loop has been detected.', array('{child}' => $childId, '{id}' => $itemId)));

            $this->db->createCommand()
                    ->insert($this->itemChildTable, array(
                        'parent' => $itemId,
                        'child' => $childId,
            ));

            return true;
        }
        else
            throw new CException(Yii::t('yii', 'Either "{parent}" or "{child}" does not exist.', array('{child}' => $childId, '{parent}' => $itemId)));
    }

    /**
     * Removes a child from its parent.
     * Note, the child item is not deleted. Only the parent-child relationship is removed.
     * @param string $itemId the parent item name
     * @param string $childId the child item name
     * @return boolean whether the removal is successful
     */
    public function removeItemChild($itemId, $childId) {
        return $this->db->createCommand()
                        ->delete($this->itemChildTable, TUtil::qc('parent').'=:parent AND '.TUtil::qc('child').'=:child', array(
                            ':parent' => $itemId,
                            ':child' => $childId
                        )) > 0;
    }

    /**
     * Returns a value indicating whether a child exists within a parent.
     * @param string $itemId the parent item name
     * @param string $childId the child item name
     * @return boolean whether the child exists
     */
    public function hasItemChild($itemId, $childId) {
        return $this->db->createCommand()
                        ->select('parent')
                        ->from($this->itemChildTable)
                        ->where(TUtil::qc('parent').'=:parent AND '.TUtil::qc('child').'=:child', array(
                            ':parent' => $itemId,
                            ':child' => $childId))
                        ->queryScalar() !== false;
    }

    /**
     * Returns the children of the specified item.
     * @param mixed $ids the parent item id. This can be either a string or an array.
     * The latter represents a list of item ids.
     * @return array all child items of the parent
     */
    public function getItemChildren($ids) {
        if (is_string($ids))
            $condition = TUtil::qc('parent').'=' . $this->db->quoteValue($ids);
        elseif (is_array($ids) && $ids !== array()) {
            foreach ($ids as &$id)
                $id = $this->db->quoteValue($id);
            $condition = TUtil::qc('parent').' IN (' . implode(', ', $ids) . ')';
        } else {
            return array();
        }
        $rows = $this->db->createCommand()
                ->select('id,name,type,description,bizrule,data,order_no')
                ->from(array(
                    $this->itemTable,
                    $this->itemChildTable
                ))
                ->where($condition . ' AND '.TUtil::qc('id').'='.TUtil::qc('child'))
                ->queryAll();

        $children = array();
        foreach ($rows as $row) {
            if (($data = @unserialize($row['data'])) === false)
                $data = null;
            $children[$row['id']] = new CAuthItem($this, $row['id'], $row['name'], $row['type'], $row['description'], $row['order_no'], $row['bizrule'], $data);
        }
        return $children;
    }

    /**
     * Assigns an authorization item to a user.
     * @param string $itemId the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @param string $bizRule the business rule to be executed when {@link checkAccess} is called
     * for this particular authorization item.
     * @param mixed $data additional data associated with this assignment
     * @return CAuthAssignment the authorization assignment information.
     * @throws CException if the item does not exist or if the item has already been assigned to the user
     */
    public function assign($itemId, $userId, $bizRule = null, $data = null) {
        if ($this->usingSqlite() && $this->getAuthItem($itemId) === null)
            throw new CException(Yii::t('yii', 'The item "{id}" does not exist.', array('{id}' => $itemId)));

        $this->db->createCommand()
                ->insert($this->assignmentTable, array(
                    'auth_id' => $itemId,
                    'user_id' => $userId,
                    'bizrule' => $bizRule,
                    'data' => serialize($data)
        ));

        AuthLog::log($itemId, $userId);
        
        return new CAuthAssignment($this, $itemId, $userId, $bizRule, $data);
    }

    /**
     * Revokes an authorization assignment from a user.
     * @param string $itemId the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return boolean whether removal is successful
     */
    public function revoke($itemId, $userId) {
        AuthLog::log($itemId, $userId, AuthLog::TYPE_DEL);
        return $this->db->createCommand()
                        ->delete($this->assignmentTable, TUtil::qc('auth_id').'=:auth_id AND '.TUtil::qc('user_id').'=:user_id', array(
                            ':auth_id' => $itemId,
                            ':user_id' => $userId
                        )) > 0;
    }

    /**
     * Returns a value indicating whether the item has been assigned to the user.
     * @param string $itemId the item name
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return boolean whether the item has been assigned to the user.
     */
    public function isAssigned($itemId, $userId) {
        return $this->db->createCommand()
                        ->select('auth_id')
                        ->from($this->assignmentTable)
                        ->where(TUtil::qc('auth_id').'=:auth_id AND '.TUtil::qc('user_id').'=:user_id', array(
                            ':auth_id' => $itemId,
                            ':user_id' => $userId))
                        ->queryScalar() !== false;
    }

    /**
     * Returns the item assignments for the specified user.
     * @param mixed $userId the user ID (see {@link IWebUser::getId})
     * @return array the item assignment information for the user. An empty array will be
     * returned if there is no item assigned to the user.
     */
    public function getAuthAssignments($userId) {
        $rows = $this->db->createCommand()
                ->select()
                ->from($this->assignmentTable)
                ->where(TUtil::qc('user_id').'=:user_id', array(':user_id' => $userId))
                ->queryAll();
        $assignments = array();
        foreach ($rows as $row) {
            if (($data = @unserialize($row['data'])) === false)
                $data = null;
            $assignments[$row['auth_id']] = new CAuthAssignment($this, $row['auth_id'], $row['user_id'], $row['bizrule'], $data);
        }
        return $assignments;
    }

    public function getDataByCache($type = null, $userId = null){
        $items = array();
        if ($type === null && $userId === null) {
            $items = $this->getData();
        } elseif ($userId === null) {
            $cacheData = $this->getData();
            foreach($cacheData as $row){
                if($row['type'] == $type)
                    $items[] = $row;
            } 
        } elseif ($type === null) {
            $cacheData = $this->getData('AUTH_USER_ALL');
            foreach($cacheData as $row){
                if($row['user_id'] == $userId)
                    $items[] = $row;
            }
        } else {
            $cacheData = $this->getData('AUTH_USER_ALL');
            foreach($cacheData as $row){
                if($row['user_id'] == $userId && $row['type'] == $type)
                    $items[] = $row;
            }
        }
        return $items;
    }
    
    public function getDataByQuery($type = null, $userId = null, $page = array()){
        if ($type === null && $userId === null) {
            $command = $this->db->createCommand()
                    ->select()
                    ->from($this->itemTable)
                    ->order('type desc,order_no,name');
            if (!empty($page)) {
                $command->limit = $page['limit'];
                $command->offset = $page['offset'];
            }
        } elseif ($userId === null) {
            $command = $this->db->createCommand()
                    ->select()
                    ->from($this->itemTable)
                    ->where(TUtil::qc('type').'=:type', array(':type' => $type))
                    ->order('type desc,order_no,name');
            if (!empty($page)) {
                $command->limit = $page['limit'];
                $command->offset = $page['offset'];
            }
        } elseif ($type === null) {
            $command = $this->db->createCommand()
                    ->select('t1.id,name,type,description,t1.order_no,t1.bizrule,t1.data')
                    ->from(array(
                        $this->itemTable . ' t1',
                        $this->assignmentTable . ' t2'
                    ))
                    ->where(TUtil::qc('user_id').'=:user_id', array(':user_id' => $userId));
        } else {
            $command = $this->db->createCommand()
                    ->select('t1.id,name,type,description,t1.order_no,t1.bizrule,t1.data')
                    ->from(array(
                        $this->itemTable . ' t1',
                        $this->assignmentTable . ' t2'
                    ))
                    ->where(TUtil::qc('type').'=:type AND '.TUtil::qc('user_id').'=:user_id', array(
                ':type' => $type,
                ':user_id' => $userId
            ));
        }
        return $command->queryAll();
    }
    
    /**
     * Returns the authorization items of the specific type and user.
     * @param integer $type the item type (0: operation, 1: task, 2: role). Defaults to null,
     * meaning returning all items regardless of their type.
     * @param mixed $userId the user ID. Defaults to null, meaning returning all items even if
     * they are not assigned to a user.
     * @return array the authorization items of the specific type.
     */
    public function getAuthItems($type = null, $userId = null, $page = array(), $condition = '' , $independent = false, $authOrg = array(),$filter = false) {
        $authOrg = !empty($authOrg) ? array_values($authOrg) : array();
        $independent =   (Yii::app()->user->logintype=="teacher")  ? Yii::app()->user->independent: false;
        if($independent) {
            $authOrg[] = $independent;
        }
        if(($independent && !$filter) || !$independent) {
            $authOrg = array_merge(array(0),$authOrg);
        }
        if ($type === null && $userId === null) {
            $command = $this->db->createCommand()
                    ->select()
                    ->from($this->itemTable)
                    ->order('type desc,order_no,name');
                if($independent) {//独立组织
                    $command->where(array('OR',  'type!='.CAuthItem::TYPE_ROLE ,array('IN',  'auth_org',  $authOrg)));
                } else if(!empty($authOrg)){
                    if(!Yii::app()->user->isAdmin) {
                        $command->where(array('IN',  'auth_org', $authOrg));
                    }
                }            
            if (!empty($page)) {
                $command->limit = $page['limit'];
                $command->offset = $page['offset'];
            }
        } elseif ($userId === null) {
            $command = $this->db->createCommand()
                    ->select()
                    ->from($this->itemTable)
                    ->order('type desc,order_no,name');
            if($condition){
                if($independent && $type == CAuthItem::TYPE_ROLE) {
                    $command->where(array('AND',  TUtil::qc('type').'='.$type ,array('IN',  'auth_org',  $authOrg),'name LIKE ' . "'%".$condition."%'" . ' OR '.TUtil::qc('description').' LIKE ' . "'%".$condition."%'"));
                } else {
                    if(Yii::app()->user->isAdmin) {
                        $command->where(array('AND',TUtil::qc('type').'='.$type ,TUtil::qc('name').' LIKE ' . "'%".$condition."%'" . ' OR '.TUtil::qc('description').' LIKE ' . "'%".$condition."%'"));
                    } else {
                        $command->where(array('AND',TUtil::qc('type').'='.$type ,array('IN',  'auth_org', $authOrg),TUtil::qc('name').' LIKE ' . "'%".$condition."%'" . ' OR '.TUtil::qc('description').' LIKE ' . "'%".$condition."%'"));
                    }
                }                
            } else {
                if($independent && $type == CAuthItem::TYPE_ROLE) {
                    $command->where(array('AND',  TUtil::qc('type').'='.$type ,array('IN',  'auth_org',  $authOrg)));
                } else {
                    if(Yii::app()->user->isAdmin) {
                        $command->where(TUtil::qc('type').'=:type', array(':type' => $type));
                    } else {
                        $command->where(array('AND', TUtil::qc('type').'='.$type ,array('IN',  'auth_org', $authOrg)));
                    }
                }
            }
            if (!empty($page)) {
                $command->limit = $page['limit'];
                $command->offset = $page['offset'];
            }
        } elseif ($type === null) {
            $command = $this->db->createCommand()
                    ->select('t1.id,name,type,description,t1.order_no,t1.bizrule,t1.data')
                    ->from(array(
                        $this->itemTable . ' t1',
                        $this->assignmentTable . ' t2'
                    ))
                    ->where(TUtil::qc('t1.id').' = '.TUtil::qc('t2.auth_id').' AND '.TUtil::qc('user_id').'=:user_id', array(':user_id' => $userId));
        } else {
            $command = $this->db->createCommand()
                    ->select('t1.id,name,type,description,t1.order_no,t1.bizrule,t1.data')
                    ->from(array(
                        $this->itemTable . ' t1',
                        $this->assignmentTable . ' t2'
                    ))
                    ->where(TUtil::qc('t1.id').' = '.TUtil::qc('t2.auth_id').' AND '.TUtil::qc('type').'=:type AND '.TUtil::qc('user_id').'=:user_id', array(
                ':type' => $type,
                ':user_id' => $userId
            ));
        }
        $items = array();
        foreach ($command->queryAll() as $row) {
            if (($data = @unserialize($row['data'])) === false)
                $data = null;
            $items[$row['id']] = new CAuthItem($this, $row['id'], $row['name'], $row['type'], $row['description'], $row['order_no'], $row['auth_org'], $row['bizrule'], $data);
        }
        return $items;
        
//        if(!empty($page)){
//            $authData = $this->getDataByQuery($type, $userId, $page);
//        } else {
//            $authData = $this->getDataByCache($type, $userId);
//        }
//        $items = array();
//        foreach ($authData as $row) {
//            if (($data = @unserialize($row['data'])) === false)
//                $data = null;
//            $items[$row['id']] = new CAuthItem($this, $row['id'], $row['name'], $row['type'], $row['description'], $row['order_no'], $row['bizrule'], $data);
//        }
//        return $items;
        
    }

    public function getTotalAuthItemsCount($type = null,$condition = null) {
        if ($type === null) {
            $command = $this->db->createCommand()
                    ->select("count(*) as count")
                    ->from($this->itemTable);
        } else {
            if($condition==null){
            $command = $this->db->createCommand()
                    ->select("count(*) as count")
                    ->from($this->itemTable)
                    ->where(TUtil::qc('type').'=:type', array(':type' => $type));  
            }else{
               $command = $this->db->createCommand()
                    ->select("count(*) as count")
                    ->from($this->itemTable)
                    ->where(TUtil::qc('type').'=:type and ('.TUtil::qc('name').' like :name or '.TUtil::qc('description').' like :description)', array(':type' => $type,":name"=>"%".$condition."%",":description"=>"%".$condition."%"));   
            }
        }
        foreach ($command->queryAll() as $row) {
            return $row['count'];
        }
        return false;
    }

    /**
     * Creates an authorization item.
     * An authorization item represents an action permission (e.g. creating a post).
     * It has three types: operation, task and role.
     * Authorization items form a hierarchy. Higher level items inheirt permissions representing
     * by lower level items.
     * @param string $name the item name. This must be a unique identifier.
     * @param integer $type the item type (0: operation, 1: task, 2: role).
     * @param string $description description of the item
     * @param string $bizRule business rule associated with the item. This is a piece of
     * PHP code that will be executed when {@link checkAccess} is called for the item.
     * @param mixed $data additional data associated with the item.
     * @return CAuthItem the authorization item
     * @throws CException if an item with the same name already exists
     */
    public function createAuthItem($name, $type, $description = '', $order_no = 0, $authOrg = 0, $bizRule = null, $data = null) {
        $this->db->createCommand()
                ->insert($this->itemTable, array(
                    'name' => $name,
                    'type' => $type,
                    'description' => $description,
                    'order_no' => $order_no,
                    'auth_org' => $authOrg,
                    'bizrule' => $bizRule,
                    'data' => serialize($data)
        ));
        if(Yii::app()->db->getDriverName()=='oci'){
           $sql = "select ".TUtil::qc('auth_item_id_seq').".currval as id from ".TUtil::qc('auth_item') ;
           $command = Yii::app()->db->createCommand($sql)->query()->read();
           $id = $command['ID'];
        }else{
             $id = $this->db->lastInsertID;
        }
        return new CAuthItem($this, $id, $name, $type, $description, $order_no, $authOrg, $bizRule, $data);
    }

    /**
     * Removes the specified authorization item.
     * @param string $id the id of the item to be removed
     * @return boolean whether the item exists in the storage and has been removed
     */
    public function removeAuthItem($id,$type = null) {
        if ($this->usingSqlite()) {
            $this->db->createCommand()
                    ->delete($this->itemChildTable, TUtil::qc('parent').'=:id1 OR '.TUtil::qc('child').'=:id2', array(
                        ':id1' => $id,
                        ':id2' => $id
            ));
            $this->db->createCommand()
                    ->delete($this->assignmentTable, TUtil::qc('itemid').'=:id', array(
                        ':id' => $id,
            ));
        }
        if($type== CAuthItem::TYPE_ROLE){
            $this->db->createCommand()->delete('data_priv_role','role_id=' . $id);
        }
        return $this->db->createCommand()
                        ->delete($this->itemTable, TUtil::qc('id').'=:id', array(
                            ':id' => $id
                        )) > 0;
    }

    /**
     * Returns the authorization item with the specified id.
     * @param string $id the id of the item
     * @return CAuthItem the authorization item. Null if the item cannot be found.
     */
    public function getAuthItem($id) {
        $row = $this->db->createCommand()
                ->select()
                ->from($this->itemTable)
                ->where(TUtil::qc('id').'=:id', array(':id' => $id))
                ->queryRow();

        if ($row !== false) {
            if (($data = @unserialize($row['data'])) === false)
                $data = null;
            return new CAuthItem($this, $row['id'], $row['name'], $row['type'], $row['description'], $row['order_no'], $row['auth_org'], $row['bizrule'], $data);
        }
        else
            return null;
    }

    /**
     * 获取角色名
     * @param string|array $id 用户ID
     * @return string|array
     */
    public static function getNameById($id = NULL) {
        if (!$id) {
            return '';
        }
        $independent = Yii::app()->user->independent;
        $authOrg = $independent ? Org::getChildById($independent) : array();
        $roleArray = Yii::app()->authManager->getAuthItems(CAuthItem::TYPE_ROLE,null,array(),'' , $independent, $authOrg);
        if (is_numeric($id)) {
            return $roleArray[$id]->name;
        } else if (is_array($id)) {
            $nameArray = array();
            foreach ($id as $key) {
                $nameArray[$key] = $roleArray[$key]->name;
            }
            return $nameArray;
        } else {
            $idArray = explode(',', trim($id, ','));
            $nameStr = '';
            foreach ($idArray as $key) {
                $nameStr .= $roleArray[$key]->name . ',';
            }
            return $nameStr;
        }

        return '';
    }

    /**
     * Returns the authorization item with the specified id.
     * @param string $name the id of the item
     * @return CAuthItem the authorization item. Null if the item cannot be found.
     */
    public function getAuthItemByName($name) {
        $row = $this->db->createCommand()
                ->select()
                ->from($this->itemTable)
                ->where(TUtil::qc('name').'=:name', array(':name' => $name))
                ->queryRow();

        if ($row !== false) {

            if (($data = @unserialize($row['data'])) === false)
                $data = null;
            return new CAuthItem($this, $row['id'], $row['name'], $row['type'], $row['description'], $row['order_no'], $row['auth_org'], $row['bizrule'], $data);
        }
        else
            return null;
    }

    /**
     * Saves an authorization item to persistent storage.
     * @param CAuthItem $item the item to be saved.
     * @param string $oldName the old item name. If null, it means the item name is not changed.
     */
    public function saveAuthItem($item, $oldName = null) {
        $this->db->createCommand()
                ->update($this->itemTable, array(
                    'name' => $item->getName(),
                    'type' => $item->getType(),
                    'description' => $item->getDescription(),
                    'order_no' => $item->getOrderNo(),
                    'bizrule' => $item->getBizRule(),
                    'data' => serialize($item->getData()),
                    'auth_org' => $item->getAuthOrg(),
                        ),TUtil::qc("id").'=:whereId', array(
                    ':whereId' => $item->getId(),
        ));
    }
    
    /**
     * 缓存权限数据
     * AUTH_ALL:所有权限
     * AUTH_USER_ALL:带有用户的权限数据
     */
    public function cacheData(){
        $authCommand = $this->db->createCommand()
                ->select()
                ->from($this->itemTable)
                ->order('type desc,order_no,name');
        $authItems = array();
        foreach ($authCommand->queryAll() as $row) {
            $authItems[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
                'description' => $row['description'],
                'order_no' => $row['order_no'],
                'bizrule' => $row['bizrule'],
                'data' => $row['data'],
            );
        }
        Yii::app()->cache->set('AUTH_ALL', $authItems);

        $userAuthcommand = $this->db->createCommand()
                ->select('t2.id,name,type,description,t2.order_no,t2.bizrule,t2.data,t1.user_id')
                ->from($this->assignmentTable . ' t1')
                ->leftJoin($this->itemTable . ' t2', '`t2`.`id`=`t1`.`auth_id`');
        
        $userAuthitems = array();
        foreach ($userAuthcommand->queryAll() as $row) {
            $userAuthitems[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
                'description' => $row['description'],
                'order_no' => $row['order_no'],
                'bizrule' => $row['bizrule'],
                'data' => $row['data'],
                'user_id' => $row['user_id'],
            );
        }
        Yii::app()->cache->set('AUTH_USER_ALL', $userAuthitems);
    }
    
    /**
     * 获取缓存数据
     * AUTH_ALL:权限缓存
     * AUTH_USER_ALL:用户授权缓存
     * @param string $type 
     */
    public function getData($type = 'AUTH_ALL'){
        if(Yii::app()->cache->get($type) === false){
            $this->cacheData();
        }
        return Yii::app()->cache->get($type);
    }

    /**
     * 获取指定用户所有authItem集合id
     * @param integer $userID
     * @return array
     */
    public function getAllAuthItems($userID) {
        if(!$userID) {
            return [];
        }
        $authItems = array_keys($this->getAuthItems(null, $userID));
        for ($i = 0; $i < 2; $i++) {
            $authItems = array_unique(array_merge($authItems, array_keys($this->getItemChildren($authItems))));
        }
        return $authItems;
    }

}
