<?php

/**
 * 全局权限行为类
 *
 */
class DataPrivBehavior extends CActiveRecordBehavior {
    /* 权限表前缀 */

    const PRIV_TABLE_PREFIX = 'data_priv_';
    /* 按用户设置权限 */
    const PRIV_TYPE_USER = 'user';
    /* 按组织设置权限 */
    const PRIV_TYPE_ORG = 'org';
    /* 按岗位设置权限 */
    const PRIV_TYPE_POSITION = 'position';
    /* 按角色设置权限 */
    const PRIV_TYPE_ROLE = 'role';

    /**
     * @var array 权限范围
     */
    protected $privScopes = array(
        self::PRIV_TYPE_USER,
        self::PRIV_TYPE_ORG,
        self::PRIV_TYPE_POSITION,
        self::PRIV_TYPE_ROLE
    );

    /**
     *
     * @var array 当前模型权限集合
     */
    protected $priv = null;

    /**
     * @var array 新权限集合
     */
    protected $privNew = null;

    /**
     * @var array 新权限类型,适用于分别保存多种类型权限
     */
    protected $privNewType = null;

    /**
     *
     * @var string 主键名称
     */
    public $pk = 'id';

    /**
     *
     * @var array 权限参数设置，形如
     * <pre>
     * array(
     *     array(
     *         'prefix' => 'priv_', //前缀
     *         'scope' => array(DataPrivBehavior::PRIV_TYPE_USER),  //权限维度
     *         'type' => ''         //权限类型
     *     ),
     * )
     * </pre>
     */
    public $config = array(array(
            'prefix' => 'priv_',
            'scope' => array(),
            'type' => ''
    ));
    public $fileType = null;

    /**
     * 初始化
     * @param CEvent $event
     * @throws CException
     */
    public function afterConstruct($event) {
        parent::afterConstruct($event);
        //检查是否设置了权限纬度
        $properties = (new ReflectionClass($this->owner))->getProperties();
        $propsArray = array();
        foreach ($properties as $prop) {
            $propsArray[] = $prop->getName();
        }

        foreach ($this->config as $k => $item) {
            if (trim($item['prefix'] == '')) {
                break;
            }
            if (!is_array($item['scope']) || sizeof($item['scope']) == 0) {
                $this->config[$k]['scope'] = $this->privScopes;
            } else {
                foreach ($item['scope'] as $scope) {
                    if (!in_array($item['prefix'] . $scope, $propsArray)) {
                        throw new CException(Yii::t('core', '权限类型名称配置错误！'));
                    }
                }
            }
        }
    }

    public function afterFind($event) {
        if ($this->owner->findMode === TActiveRecord::FIND_ONE) {
            foreach ($this->config as $item) {
                $prefix = $item['prefix'];
                $type = $item['type'];
                $privArray = $this->getPriv($type);
                foreach ($privArray as $scope => $value) {
                    $this->owner->{$prefix . $scope} = $value;
                }
            }
        }
        parent::afterFind($event);
    }

    public function afterSave($event) {
        if (!empty($this->privNew)) {
            $this->savePriv();
        }
    }

    public function afterDelete($event) {
        $this->deleteAllPriv();
    }

    /**
     * 删除权限数据
     * @param string $scope
     * @param string $type
     */
    protected function deletePriv($scope, $type = '', $fieldId = '') {
        $privTable = self::PRIV_TABLE_PREFIX . $scope;
        $conditions = array('AND', TUtil::qc("model") . '=:model', TUtil::qc("pk") . '=:pk');
        $params = array(':model' => $this->owner->tableName(), ':pk' => $this->owner->{$this->pk});
        if ($type) {
            $conditions[] = TUtil::qc("type") . '=:type';
            $params[':type'] = $type;
        }
        if ($fieldId) {
            $select = $scope . '_id';
            $fieldArray = is_array($fieldId) ? $fieldId : explode(",", trim($fieldId, ","));
            $privCondition = array('IN', $select, $fieldArray);
            $conditions[] = $privCondition;
        }
        Yii::app()->db->createCommand()->delete($privTable, $conditions, $params);
    }

    protected function deleteAllPriv($type = '') {
        foreach ($this->privScopes as $scope) {
            $this->deletePriv($scope, $type);
        }
    }

    /**
     * 插入权限数据
     * @param array $params
     * @return bool
     * @throws CDbException
     */
    protected function insert($params = array()) {
        $transaction = Yii::app()->db->beginTransaction();
        $items = explode(',', rtrim($params['data'], ','));
        if (empty($items)) {
            return false;
        }
        try {
            foreach ($items as $item) {
                Yii::app()->db->createCommand()->insert($params['table'], array(
                    $params['field'] => $item,
                    'model' => $this->owner->tableName(),
                    'pk' => $this->owner->attributes[$this->pk],
                    'type' => $params['type'],
                ));
            }
            $transaction->commit();
            return true;
        } catch (CException $e) {
            $transaction->rollBack();
        }
        return false;
    }

    /**
     * 设置权限
     * @param array $priv
     * @return $this
     */
    public function setPriv($privData, $privType = null) {
        $this->privNew = $privData;
        $this->privNewType = $privType;
        return $this;
    }

    /**
     * 保存权限
     * @param string $actionName  //值可以设置为'delete(删除权限)、create(添加权限)、""(覆盖之前的授权)'
     * @return integer $result 权限变更条数
     */
    public function savePriv($actionName = "") {
        $ret = 0;
        foreach ($this->config as $item) {
            if ($this->privNewType !== null) {
                $type = trim($this->privNewType);
            } else {
                $type = trim($item['type']);
            }
            $prefix = $item['prefix'];

            //获取当前数据权限
            $priv = $this->getPriv($type, true);

            if (!is_array($item['scope']) || sizeof($item['scope']) == 0) {
                $item['scope'] = $this->privScopes;
            }
            if ($actionName == "") {
                foreach ($this->privScopes as $scopes) {
                    if (trim($this->privNew[$prefix . $scopes]) == "") {
                        $this->deletePriv($scopes, $type);
                    }
                }
            }
            foreach ($item['scope'] as $scope) {
                if ($this->privNew[$prefix . $scope] !== '' &&
                        (rtrim($priv[$scope], ',') !== rtrim($this->privNew[$prefix . $scope], ',') || ($actionName == "delete"))
                ) {
                    if ($actionName != "create") {
                        $this->deletePriv($scope, $type, $actionName == "" ? "" : $this->privNew[$prefix . $scope]);
                    }
                    if ($actionName != "delete") {
                        $oldPriv = trim($priv[$scope], ',') != "" ? explode(",", trim($priv[$scope], ',')) : array();
                        $newPriv = trim($this->privNew[$prefix . $scope], ',') != "" ? explode(",", trim($this->privNew[$prefix . $scope], ',')) : array();
                        $diffArray = array_diff($newPriv, $oldPriv);
                        $ret += $this->insert(array(
                            'type' => $type,
                            'data' => $actionName == "" ? $this->privNew[$prefix . $scope] : implode(",", $diffArray),
                            'table' => self::PRIV_TABLE_PREFIX . $scope,
                            'field' => $scope . '_id',
                        ));
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * 获取当前Model的权限配置
     *
     * @param string $type 权限类型
     * @return array 用户、角色、部门三类权限集合
     */
    public function getPriv($type = '', $afresh = false) {
        if ($afresh) {
            $this->priv = null;
        }
        if (!isset($this->priv)) {
            $this->priv = array();
            foreach ($this->privScopes as $scope) {
                $list = '';
                $select = $scope . '_id';
                $from = self::PRIV_TABLE_PREFIX . $scope;
                $conditions = array('AND', TUtil::qc('model') . '=:model', TUtil::qc('pk') . '=:pk');
                $params = array(':model' => $this->owner->tableName(), ':pk' => $this->owner->attributes[$this->pk]);
                if ($type) {
                    $conditions[] = TUtil::qc("type") . '=:type';
                    $params[':type'] = $type;
                }
                if ($this->owner->attributes[$this->pk]) {
                    $rows = Yii::app()->db->createCommand()
                            ->select($select)
                            ->from($from)
                            ->where($conditions, $params)
                            ->order($select)
                            ->query();
                    foreach ($rows as $row) {
                        $list .= $row[$select] . ',';
                    }
                }
                $this->priv[$scope] = $list;
            }
        }
        return $this->priv;
    }

    /**
     * 获取当前用户或者指定用户基于某种类型的所有权限的Model主键的集合
     *
     * @param string $type 权限类型
     * @return array 按权限类型分类的模型相关表的主键ID
     */
    public function getRecordData($type = '', $user = null) {
        $tmp = array();
        if ($user == null) {
            $user = Yii::app()->user->id;
            $org = Yii::app()->user->org["all"];
            $role = array_keys(Yii::app()->user->role);
            $position = array_keys(Yii::app()->user->position);
        } else {
            $userModel = User::model()->with('userOrg')->findByPk($user);
            $org = $userModel->org['all'];
            $role = array_keys($userModel->getRole());
            $position = array_keys($userModel->getPosition());
        }

        foreach ($this->privScopes as $scope) {
            $value = ${$scope};
            if (empty($value)) {
                continue;
            }

            $field = $scope . '_id';
            $table = self::PRIV_TABLE_PREFIX . $scope;
            $params = array();
            if (is_array($value)) {
                $privCondition = array('IN', $field, $value);
            } else {
                $privCondition = TUtil::qc($field) . '=:' . $field;
                $params[":$field"] = $value;
            }
            $conditions = array('AND', TUtil::qc("model") . '=:model', $privCondition);
            $params[':model'] = $this->owner->tableName();
            if ($type) {
                $conditions[] = TUtil::qc("type") . '=:type';
                $params[':type'] = $type;
            }
            $rows = Yii::app()->db->createCommand()
                    ->select('pk, type')
                    ->from($table)
                    ->where($conditions, $params)
                    ->queryAll();
            foreach ($rows as $row) {
                if ($row['type'] == "") {
                    $tmp[] = $row['pk'];
                } else {
                    $tmp[$row['type']][] = $row['pk'];
                }
            }
        }
        return $tmp;
    }

    /**
     * 为表联合方式查询有权限的业务数据提供条件
     *
     * @param int $user 要查询权限的用户，默认为当前用户
     * @param CDbCriteria $criteria
     * @return \CDbCriteria
     */
    public function getPrivCriteria($user = NULL, $criteria = NULL, $type = '') {
        if ($user == NULL) {
            $user = Yii::app()->user->id;
            $org = Yii::app()->user->org["all"];
            $role = array_keys(Yii::app()->user->role);
            $position = array_keys(Yii::app()->user->position);
        } else {
            $userModel = User::model()->findByPk($user);
            $org = $userModel->org["all"];
            $role = array_keys($userModel->getRole());
            $position = array_keys($userModel->getPosition());
        }
        $role = TUtil::trim($role);

        if ($criteria == NULL) {
            $criteria = new CDbCriteria ();
        }

        $select = '';
        $model = $this->owner->tableName();
        foreach ($this->owner->getMetaData()->columns as $columns) {
            if (!strpos(strtolower($columns->dbType), 'lob')) {
                $select .= TUtil::qc("t." . $columns->name) . ",";
            }
        }
        $select = rtrim($select, ',');
        $criteria->select = $select;
        $criteriaChild = new CDbCriteria();
        foreach ($this->config as $item) {
            if ($item['type'] == $type) {
                if (!is_array($item['scope']) || sizeof($item['scope']) == 0) {
                    $item['scope'] = $this->privScopes;
                }

                $ids = array();
                foreach ($item['scope'] as $scope) {
                    $table = self::PRIV_TABLE_PREFIX . $scope;
                    $field = $scope . '_id';
                    $sql = "select " . TUtil::qc($table . ".pk") . " FROM " . TUtil::qc("{$table}") . " WHERE ";
                    $scopeValue = ${$scope};
                    if (!empty($scopeValue)) {
                        if (is_numeric($scopeValue)) {
                            $sql .= TUtil::qc($table . "." . $field) . " = {$scopeValue} AND " . TUtil::qc($table . ".model") . "= '{$model}' AND " . TUtil::qc($table . ".pk") . " IS NOT NULL";
                        } else if (is_array($scopeValue)) {
                            $sql .= TUtil::qc($table . "." . $field) . ' IN (\'' . implode('\', \'', array_unique($scopeValue)) . '\') AND ' . TUtil::qc($table . ".model") . "= '{$model}' AND " . TUtil::qc($table . ".pk") . " IS NOT NULL";
                        }
                        $privData = Yii::app()->db->createCommand($sql)->queryAll();
                    }

                    if (!empty($privData)) {
                        foreach ($privData as $priv) {
                            $ids[] = $priv['pk'];
                        }
                    }
                }

                $criteriaChild->condition = TUtil::qc("t.id") . ' IN (\'' . implode('\', \'', array_unique($ids)) . '\')'; //mb_substr($criteriaChild->condition, 0,-4);
                $criteria->mergeWith($criteriaChild);
                $criteria->distinct = true;
                $criteria->condition = '(' . $criteria->condition . ')';
            }
        }
        return $criteria;
    }

    /**
     * 返回拥有数据权限的用户
     * @return array
     */
    public function getPrivUsers($type = null) {
        $priv = ($type == null) ? $this->getPriv() : $this->getPriv($type);
        return User::getUsersByScope($priv, User::UCT_KEY_ARRAY);
    }

    /**
     * 返回当前用户拥有权限的数据pk
     */
    public function getPrivPks($user = NULL, $type = '') {
        if ($user == NULL) {
            $user = Yii::app()->user->id;
            $org = Yii::app()->user->org["all"];
            $role = array_keys(Yii::app()->user->role);
            $position = array_keys(Yii::app()->user->position);
        } else {
            $userModel = User::model()->findByPk($user);
            $org = $userModel->org["all"];
            $role = array_keys($userModel->getRole());
            $position = array_keys($userModel->getPosition());
        }
        $role = TUtil::trim($role);
        $model = $this->owner->tableName();
        foreach ($this->config as $item) {
            if ($item['type'] == $type) {
                if (!is_array($item['scope']) || sizeof($item['scope']) == 0) {
                    $item['scope'] = $this->privScopes;
                }
                $sql = '';
                $ids = array();
                foreach ($item['scope'] as $scope) {
                    $table = self::PRIV_TABLE_PREFIX . $scope;
                    $field = $scope . '_id';
                    $sql = "select " . TUtil::qc($table . ".pk") . " FROM " . TUtil::qc("{$table}") . " WHERE ";
                    $scopeValue = ${$scope};
                    if (!empty($scopeValue)) {
                        if (is_numeric($scopeValue)) {
                            $sql .= TUtil::qc($table . "." . $field) . " = {$scopeValue} AND " . TUtil::qc($table . ".model") . "= '{$model}' AND " . TUtil::qc($table . ".pk") . " IS NOT NULL";
                        } else if (is_array($scopeValue)) {
                            $sql .= TUtil::qc($table . "." . $field) . ' IN (\'' . implode('\', \'', array_unique($scopeValue)) . '\') AND ' . TUtil::qc($table . ".model") . "= '{$model}' AND " . TUtil::qc($table . ".pk") . " IS NOT NULL";
                        }
                        $privData = Yii::app()->db->createCommand($sql)->queryAll();
                    }
                    if (!empty($privData)) {
                        foreach ($privData as $priv) {
                            $ids[] = $priv['pk'];
                        }
                    }
                }
            }
        }
        return $ids;
    }

}
