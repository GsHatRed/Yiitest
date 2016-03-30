<?php

/**
 * 主从读写分离
 *
 * @author lx
 */
class TActiveRecord extends CActiveRecord {
    
    const MODEL_PRIV_READ = 1;
    const MODEL_PRIV_UPDATE = 2;
    const MODEL_PRIV_DELETE = 3;

    const FIND_ONE = 'find_one';
    const FIND_ALL = 'find_all';

    //取一条或取多条
    private $_findMode = '';
    
    //模型拥有者属性名称
    public $ownerAttributeName = 'create_user';



    /**
     * 切换数据库连接
     * @param string $mode
     * @throws CDbException
     * @return
     */
    protected function switchConnection($mode = self::MODE_READ) {
        if(!$this->_enableMasterSlave) {
            $mode = self::MODE_WRITE;
        }
        if ($this->mode == $mode && self::$dataBase[$mode] !== null)
            return self::$dataBase[$mode];

        if ($mode == self::MODE_WRITE) {
            self::$dataBase[$mode] = Yii::app()->getDb();
        } else {
            $slaves = Yii::app()->params['db_slaves'];
            //随机slave
            $slaveKey = array_rand($slaves);
            $config = $slaves[$slaveKey];

            if ($dbComponent = Yii::createComponent($config)) {
                self::$dataBase[$mode] = $dbComponent;
            }
        }
        if (self::$dataBase[$mode] instanceof CDbConnection) {
            self::$dataBase[$mode]->setActive(true);
            return self::$dataBase[$mode];
        } else
            throw new CDbException("数据库连接创建失败");
    }
    /**
     * 设置获取查询模式
     */
    public function setFindMode($findMode) {
        $this->_findMode = $findMode;
        return $this;
    }
    /**
     * 获取查询模式
     */
    public function getFindMode(){
        return $this->_findMode;
    }
        
    /**
     * 检查数据权限(RUD)
     * @param type $priv
     * @return boolean
     */
    public function checkPriv($priv = self::MODEL_PRIV_READ) {
        switch($priv) {
            case self::MODEL_PRIV_READ:
                $ret = true;
                break;
            case self::MODEL_PRIV_UPDATE:
            case self::MODEL_PRIV_DELETE;
                $ret = ($this->attributes[$this->ownerAttributeName] && $this->attributes[$this->ownerAttributeName] != 0) ?  $this->attributes[$this->ownerAttributeName] == Yii::app()->user->id : true;
                break;
            default:
                $ret = true;
        }
        return $ret;
    }
    
    /**
     * 检查附件权限
     * @param type $priv
     * @return type
     */
    public function checkAttachmentPriv($priv = self::MODEL_PRIV_READ) {
        return $this->checkPriv(self::MODEL_PRIV_READ);
    }
    
    /**
     * 自定义初始化record方法，被populateRecord方法调用
     * @param array $attributes
     * @return \class
     */
    protected function instantiate($attributes) {
        $class = get_class($this);
        $model = new $class(null);
        $findMode = $this->getFindMode();
        $model->setFindMode($findMode);
        return $model;
    }

    public function findByPk($pk,$condition='',$params=array()) {
        $this->setFindMode(self::FIND_ONE);
        return parent::findByPk($pk,$condition,$params);
    }
    public function find($condition='',$params=array()) {
        $this->setFindMode(self::FIND_ONE);
        return parent::find($condition,$params);
    }
    public function findByAttributes($attributes,$condition='',$params=array()) {
        $this->setFindMode(self::FIND_ONE);
        return parent::findByAttributes($attributes,$condition,$params);
    }
    public function findBySql($sql,$params=array()) {
        $this->setFindMode(self::FIND_ONE);
        return parent::findBySql($sql,$params);
    }
}
