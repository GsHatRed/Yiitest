<?php
/**
 * 数据库连接类，支持数据库主从模式
 *
 * 在main.php中配置db组件属性
 * <pre>
 * 'slaves' => array(
 *     array('connectionString'=>'mysql://<slave01>'),
 *     array('connectionString'=>'mysql://<slave02>'),
 *     ...
 * )
 * </pre>
 */

class TDbConnection extends CDbConnection{
    /**
     * @var string 缓存组件
     */
    public $cacheID = 'cache';

    /**
     * @var array 从库配置数组
     */
    public $slaves = array();

    /**
     * @var int 死亡标记时间，如果从数据库连接失败,此时间间隔内不尝试再使用此连接
     */
    public $markDeadSeconds = 600;


    /**
     * @var array 从库自动继承主库的属性
     */
    private $_autoExtendsProperty = array(
        'username', 'password', 'charset', 'tablePrefix', 'emulatePrepare', 'enableParamLogging', 'attributes',
    );

    /**
     * @var slave连接实例
     */
    private $_slave = null;

    /**
     * @var bool 强制使用主库
     */
    private $_forceUseMaster = false;

    /**
     * 重载方法，实现读写分离判断
     *
     * @param string $sql
     * @return CDbCommand
     */
    public function createCommand($sql = null)
    {
        if (
            !$this->_forceUseMaster && $this->slaves && is_string($sql) && !$this->getCurrentTransaction()
            && self::isReadOperation($sql) && ($slave = $this->getSlave()) && $slave != null
        ) {
            return $slave->createCommand($sql);
        }
        return parent::createCommand($sql);
    }

    /**
     * 强制使用主库，某些场景下为了获取数据的及时性
     *
     * @param bool $value
     */
    public function forceUseMaster($value = false)
    {
        $this->_forceUseMaster = $value;
    }

    /**
     * 获得从服务器连接资源
     * @return CDbConnection
     * */
    public function getSlave() {

        if (!isset($this->_slave)) {

            shuffle($this->slaves);
            foreach ($this->slaves as $slaveConfig) {
                if ($this->_isDeadServer($slaveConfig['connectionString'])) {
                    continue;
                }
                if (!isset($slaveConfig['class']))
                    $slaveConfig['class'] = 'CDbConnection';

                $slaveConfig['autoConnect'] = false;
                // 自动属性继承
                foreach ($this->_autoExtendsProperty as $property) {
                    isset($slaveConfig[$property]) || $slaveConfig[$property] = $this->$property;
                }

                try {
                    if ($slave = Yii::createComponent($slaveConfig)) {
                        $slave->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                        $slave->setActive(true);
                        $this->_slave = $slave;
                        break;
                    }
                } catch (Exception $e) {
                    var_dump($e);
                    $this->_markDeadServer($slaveConfig['connectionString']);
                    Yii::log("Slave database connection failed!\n\tConnection string:{$slaveConfig['connectionString']}", 'warning');
                    continue;
                }
            }
        }
        return $this->_slave;
    }


    /**
     * 检测读操作 sql 语句
     *
     * 关键字： SELECT,DECRIBE,SHOW ...
     * 写操作:UPDATE,INSERT,DELETE ...
     * */
    public static function isReadOperation($sql) {
        $sql = substr(ltrim($sql), 0, 10);
        $sql = str_ireplace(array('SELECT', 'SHOW', 'DESCRIBE', 'PRAGMA'), '^O^', $sql); //^O^,magic smile
        return strpos($sql, '^O^') === 0;
    }

    /**
     * 检测从服务器是否被标记 失败.
     */
    private function _isDeadServer($c) {
        $cache = Yii::app()->getComponent($this->cacheID);
        if ($cache && $cache->get('DeadServer::' . $c) == 1) {
            return true;
        }
        return false;
    }

    /**
     * 标记失败的slaves.
     */
    private function _markDeadServer($c) {
        $cache = Yii::app()->getComponent($this->cacheID);
        if ($cache) {
            $cache->set('DeadServer::' . $c, 1, $this->markDeadSeconds);
        }
    }
}