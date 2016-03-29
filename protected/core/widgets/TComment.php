<?php

/**
 * 模块评论组件
 * @author xxb <xxb@tongda2000.com>
 */
Yii::import('application.modules.sys.models.SysComment');
Yii::import('core.widgets.TWidget');

class TComment extends TWidget {

    public $cssFile = "comment.css";
    public $scriptFile = "comment.js";
    private $_model; //模型表名称
    private $_pk; //主键
    public $enableEdit = true; //是否可发表意见
    public $enableReply = true; //允许对评论回复
    public $enabelCaptcha = false; //是否开启验证码
    public $enableRating = false; //是否开启评级
    public $model;
    public $pkName = "id";
    private $_listView = "core.views.comment.list"; //评论内容列表视图
    private $_listViewTemplate = "core.views.comment._list"; //评论内容列表视图
    private $_formView = "core.views.comment.form"; //评论表单视图

    //组件初始化时调用

    public function init() {
        parent::init();
        $this->_model = $this->model->owner->tableName();
        $this->_pk = $this->model->owner->attributes[$this->pkName];
    }

    //获取评论内容多维数组，chrildren为评论的回复。
    private function _getComment($parent_id = 0) {
        $criteria = new CDbCriteria();
        $criteria->addCondition(TUtil::qc('model') . "=:model");
        $criteria->addCondition(TUtil::qc('pk') . "=:pk");
        $criteria->addCondition(TUtil::qc('parent_id') . "=:parent_id");
        if ($parent_id == 0)
            $criteria->order = TUtil::qc('create_time') . " desc";
        else
            $criteria->order = TUtil::qc('create_time') . " asc";
        $criteria->params = array(":model" => $this->_model, ":pk" => $this->_pk, ":parent_id" => $parent_id);
        $data = SysComment::model()->findAll($criteria);
        $comments = array();
        $i = 0;
        foreach ($data as $value) {
            $comments[$i]["id"] = $value["id"];
            $comments[$i]["parent_id"] = $value["parent_id"];
            $comments[$i]["rating"] = $value["rating"];
            $comments[$i]["content"] = $value["content"];
            $comments[$i]["create_time"] = $value["create_time"];
            $comments[$i]["create_user"] = $value["create_user"];
            $userInfo = User::model()->getUserInfoById($value["create_user"]);
            $comments[$i]["user_gender"] = $userInfo["gender"];
            $comments[$i]["user_name"] = $userInfo["user_name"];
            $comments[$i]["user_avatar"] = User::getAvatar($value["create_user"], $userInfo['avatar'], 'm', $userInfo['gender'], TOrgUtil::userIsOnline($value["create_user"]));
            $comments[$i]["chrildren"] = $this->_getComment($value["id"]);
            $comments[$i]["enableReply"] = $this->enableReply;
            $i++;
        }
        return $comments;
    }

    //执行组件
    public function run() {
        $data = $this->_getComment();
        echo "<legend>讨论区</legend>";
        if (empty($data)) {
            echo "<center><div class='well well-small'>暂无会签意见!</div>  </center>";
        } else {
            $this->render($this->_listView, array(
                "dataArr" => $data,
                'enableReply' => $this->enableReply,
                'model' => $this->_model,
                'pk' => $this->_pk,
                'viewTemplate' => $this->_listViewTemplate,
            ));
        }
        if ($this->enableEdit) {
            $loginUser = Yii::app()->user->id;
            $userInfo = User::model()->getUserInfoById($loginUser);
            $avatar = User::getAvatar($loginUser, $userInfo['avatar'], 'm', $userInfo['gender'], TOrgUtil::userIsOnline($loginUser));
            $this->render($this->_formView, array(
                "model" => $this->_model,
                "pk" => $this->_pk,
                "avatar" => $avatar,
            ));
        }
    }

}

?>
