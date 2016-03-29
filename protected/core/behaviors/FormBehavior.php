<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
Yii::import('application.modules.document.models.*');
Yii::import('application.modules.workflow.models.*');
Yii::import('application.modules.form.models.*');
Yii::import('application.modules.form.components.*');
include_once('protected/modules/form/phpQuery/phpQuery/phpQuery.php');

class FormBehavior extends CActiveRecordBehavior {

    /**
     *
     * @var string 主键名称
     */
    public $pk = 'id';

    /**
     *
     * @var string 模型名称
     */
    private $_model;

    /**
     *
     * @var string 模型pk
     */
    private $_modelPk;

    /**
     * @var string 表单前缀
     */
    public $prefix = 'form_data_';

    private function _init() {
        $this->_model = $this->owner->tableName();
        $this->_modelPk = $this->owner->attributes[$this->pk];
    }

    public function saveToMap($formID, $dataSyn = false, $formData = array()) {
        $this->_init();
        $formDataId = $this->initFormData($formID, $dataSyn, $formData);
        $mapModel = new FormDataMap;
        $mapModel->pk = $this->_modelPk;
        $mapModel->model = $this->_model;
        $mapModel->form_id = $formID;
        $mapModel->form_data_id = $formDataId;
        if ($mapModel->save()) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 判断是否新建了表单
     */

    public function getFormId() {
        $this->_init();
        $mapInfo = FormDataMap::model()->find(TUtil::qc("pk") . '=:pk and ' . TUtil::qc("model") . '=:model', array(':pk' => $this->_modelPk, ':model' => $this->_model));
        return $mapInfo ? $mapInfo->form_id : $mapInfo;
    }

    public function getMapInfo($formID) {
        $this->_init();
        $mapInfo = FormDataMap::model()->find(TUtil::qc("pk") . '=:pk and ' . TUtil::qc("form_id") . '=:form_id and ' . TUtil::qc("model") . '=:model', array(':pk' => $this->_modelPk, ':form_id' => $formID, ':model' => $this->_model));
        return $mapInfo ? $mapInfo->form_data_id : $mapInfo;
    }

    /**
     * 数据表初始化
     * @param $formID 表单id, $dataSyn 是否同步映射, $formData 初始表单数据
     */
    public function initFormData($formID, $dataSyn = false, $formData = array()) {
        $postfix = Form::getIdByFormId($formID);
        $table = $this->prefix . $postfix;
        if (!TFormUtil::whetherTableExist($this->prefix, $formID)) {
            $formType = Form::getFormType($formID);
            $model = $formType == 2 ? 'FormHtmlField' : 'FormAipField';
            TFormUtil::dynamicCreateTable($model, $this->prefix, $formID);
        }
        $rs = Yii::app()->db->getSchema()->getTable($table)->getColumnNames();
        $columnArr = array();
        $mapArr = array();
        if ($dataSyn) {
            $modelPk = $this->owner->attributes[$this->pk];
            if ($this->_model == "document") {
                Yii::import("document.models.*");
                $model = Document::model()->findByPk($modelPk);
                $mapData = $model->type->getMapping();
                foreach ($mapData as $map) {
                    $mapArr["data_" . $map['form']] = $model->{$map['doc']};
                }
            } else if ($this->_model == "collect") {
                Yii::import("collect.models.*");
                $model = Collect::model()->findByPk($modelPk);
                $sysApplication = SysApplication::model()->find(TUtil::qc('model') . "=" . TUtil::qv('collect'));
                $mapData = $sysApplication != null ? $sysApplication->getMapping() : array();
                if (!empty($mapData)) {
                    foreach ($mapData as $map) {
                        $mapArr["data_" . $map['form']] = $model->{$map['doc']};
                    }
                }
            } else if ($this->_model == "sv_task") {
                Yii::import("supervision.models.*");
                $model = SVTask::model()->findByPk($modelPk);
                $sysApplication = SysApplication::model()->find(TUtil::qc('model') . "=" . TUtil::qv('sv_task'));
                $mapData = $sysApplication != null ? $sysApplication->getMapping() : array();
                if (!empty($mapData)) {
                    foreach ($mapData as $map) {
                        $mapArr["data_" . $map['form']] = $model->{$map['doc']};
                    }
                }
            } else {
                $runHookModel = WfRtRunHook::model()->find(TUtil::qc('application_id') . "=:application_id", array(":application_id" => $modelPk));
                if ($runHookModel != null) {
                    $hookModel = WfRtHook::model()->find(TUtil::qc('hook_model') . "=:hook_model and status=1", array(":hook_model" => $runHookModel->model));
                    if ($hookModel != null) {
                        $mapData = $hookModel->getMapping();
                        $classModel = $hookModel->module_class;
                        $moduleName = explode("/", trim($hookModel->application->app_path, "/"));
                        Yii::import($moduleName[0] . ".models.*");
                        if (class_exists($classModel)) {
                            $model = $classModel::model()->findByPk($runHookModel->pk);
                            foreach ($mapData as $map) {
                                $mapArr["data_" . $map['form']] = $model->{$map['field']};
                            }
                        }
                    }
                }
            }
            if (!empty($formData)) {
                foreach ($formData as $form) {
                    if ($form['field_value'] != "")
                        $mapArr["data_" . $form['field_id']] = $form['field_value'];
                }
            }
        }
        foreach ($rs as $field) {
            if ($field == 'id') {
                continue;
            }
            $columnArr[$field] = !array_key_exists($field, $mapArr) ? '' : $mapArr[$field];
            if ($field == 'content') {
                $columnArr[$field] = FormAip::getBlobData($formID);
            }
        }
        if (is_array($columnArr) && !empty($columnArr)) {
            Yii::app()->db->createCommand()->insert($table, $columnArr);
            if (Yii::app()->db->getDriverName() == 'oci') {
                // $id =Yii::app()->db->createCommand()->select('max('.TUtil::qc("id").') as id')->from($table)->query()->read()['ID'];
                $sql = "select " . TUtil::qc($table . '_id_seq') . ".currval as id from " . TUtil::qc($table);
                $command = Yii::app()->db->createCommand($sql)->query()->read();
                $id = $command['ID'];
            } else {
                $id = Yii::app()->db->lastInsertID;
            }
            return $id;
        } else {
            return false;
        }
    }

    /**
     * 获取表单数据
     * @param $formID 表单ID
     */
    public function getFormData($formID) {
        $postfix = Form::getIdByFormId($formID);
        if (TFormUtil::whetherTableExist($this->prefix, $formID)) {
            $id = $this->getMapInfo($formID);
            if ($id !== null) {
                $rs = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from($this->prefix . $postfix)
                        ->where(TUtil::qc("id") . '=:id', array(':id' => $id))
                        ->queryRow();
            }
        }
        return $rs;
    }

    /**
     * 获取字段数据
     * @param string $formID 
     * @param string $dataKey 数据键值类型
     * @return array 
     */
    public function getFormFieldData($formID, $dataKey = 'id', $allowEmpty = true) {
        $fieldData = array();

        $formData = $this->getFormData($formID);
        $fields = FormHtmlField::getFields($formID);
        $dataId = $this->getMapInfo($formID);
        foreach ($fields as $field) {
            $value = '';
            if ($field['field_type'] == 'countersign') {
                $value = array();
                $signData = TFormUtil::getSignData($field['id'], $dataId);
                foreach ($signData as $sd) {
                    $value[] = array(
                        'content' => $sd['signcontent'],
                        'user' => $sd['signuser'],
                        'time' => $sd['signtime'],
                    );
                }
            } elseif (in_array($field['field_type'], array('writesign', 'textarea'))) {
                if (Yii::app()->db->getDriverName() == 'oci') {
                    $value = stream_get_contents($formData['data_' . $field['id']]);
                } else {
                    $value = $formData['data_' . $field['id']];
                }
            } else {
                $value = $formData['data_' . $field['id']];
            }
            if ($allowEmpty || !empty($value)) {
                if ($dataKey == 'id') {
                    $fieldData[$field['id']] = $value;
                } elseif ($dataKey == 'name') {
                    $fieldData[$field['field_name']] = $value;
                }
            }
        }
        return $fieldData;
    }

    public function getFormJsonData($formID) {
        $fieldInfo = FormHtmlField::getFields($formID);
        $formData = $this->getFormData($formID);
        $dataId = $this->getMapInfo($formID);
        foreach ($fieldInfo as $info) {
            $id = $info['id'];
            $name = $info['field_name'];
            $type = $info['field_type'];
            $value = $formData['data_' . $id];
//                if ($type == 'countersign') {
//                    $value = array();
//                    $countersign = $this->getNewSignData($signInfo); //TFormUtil::getSignData($id, $dataId);
//                    if (!empty($countersign)) {
//                        foreach ($countersign as $row) {
//                            $value[] = array('signcontent' => $row['signcontent'], 'signuser' => $row['signuser'], 'signtime' => $row['signtime']);
//                        }
//                    }
//                }
            if ($type == 'listview') {
                $value = array();
                $listview = TFormUtil::getListViewData($id, $dataId);
                if (!empty($listview)) {
                    $value = $listview;
                }
            }
            $fieldAttr[] = array('id' => $id, 'name' => $name, 'type' => $type, 'value' => $value);
        }
        return CJSON::encode($fieldAttr);
    }

    public function getNewSignData($signId) {
        $newSignData = array();
        if ($signId) {
            $signData = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('form_data_countersign')
                    ->where(TUtil::qc("id") . '=:id', array(':id' => $signId))
                    ->queryRow();
            if (!empty($signData)) {
                $newSignData['signcontent'] = $signData['signcontent'];
                $newSignData['signuser'] = $signData['signuser'];
                $newSignData['signtime'] = $signData['signtime'];
                $newSignData['user_id'] = $signData['user_id'];
                return $newSignData;
            }
        }
    }

    public function generateExpression($fieldId, $formData) {
        global $params;
        $params = array();
        $model = FormHtmlField::getFieldAttr($fieldId);
        if ($model && is_array($model)) {
            $fieldAttr = unserialize($model['field_attr']);
            $relations = $fieldAttr['relations'];
            if (is_array($relations)) {
                foreach ($relations as $bindFieldId => $rule) {
                    $params[$rule] = $formData[$bindFieldId]['value'];
                }
            }
            $rule = SysRule::model()->findByPk($fieldAttr['value']);
            $expression = trim(preg_replace_callback("/{[@#](.*?)}/", create_function('$matches', 'return $GLOBALS["params"][is_array($matches) ? $matches[1] : $matches];'), $rule->expression));
            return $expression;
        }
    }

    /**
     * 表单数据更新
     * @param $formID 表单id
     * @param $formData 表单数据 例如：{'data_1':{'name':'', 'type':'', value:''},}
     * @param $dataID 表单数据id
     * @todo 可写字段验证
     * @return boolean
     */
    public function formDataSave($formID, $formData, $dataID = null, $accessFields = array()) {
        $prefix = $this->prefix;
        $postfix = Form::getIdByFormId($formID);
        $table = $prefix . $postfix;
        $accessFields = (is_array($accessFields) && !empty($accessFields) && array_key_exists($formID, $accessFields)) ? $accessFields[$formID] : array();
        if (!TFormUtil::whetherTableExist($prefix, $formID)) {
            TFormUtil::dynamicCreateTable('FormHtmlField', $prefix, $formID);
        }
        $columns = $this->getFormDataTableField($table);
        if ($dataID != null && is_array($formData)) {
            $newSignInfo = array();
            foreach ($formData as $fieldID => $fieldAttr) {
                $name = $fieldAttr['name'];
                $type = $fieldAttr['type'];
                $value = $fieldAttr['value'];
                if (!in_array($fieldID, $columns)) {
                    continue;
                }
                if (!is_array($value)) {
                    $value = htmlspecialchars($value);
                }
                if (is_array($value)) {
                    if ($type === 'countersign') {
                        foreach ($value as $signInfo) {
                            if ($signInfo) {
                                if (is_string($signInfo)) {
                                    $value = $signInfo;
                                } else if (is_array($signInfo)) {
                                    foreach ($signInfo as $signField => $signValue) {
                                        if ($signField == 'field_id') {
                                            $value = $signValue;
                                        }
                                        if ($signField == 'id') {
                                            $id = $signValue;
                                            continue;
                                        }
                                        $signColumnArr[$signField] = $signValue;
                                    }
                                    if (trim($signColumnArr['signcontent']) || $signColumnArr['signdata'] || Yii::app()->params['allow_countersign_empty']) {
                                        $where = array(':id' => $id);
                                        if ($id) {
                                            $newSignInfo[$fieldID] = $id;
                                            Yii::app()->db->createCommand()->update('form_data_countersign', $signColumnArr, TUtil::qc("id") . '=:id', $where);
                                        } else {
                                            $sql = "select " . TUtil::qc('signtime') . " from form_data_countersign where " . TUtil::qc('form_data_id') . " = " . TUtil::qv($signColumnArr['form_data_id']) . " and " . TUtil::qc('field_id') . " = " . TUtil::qv($signColumnArr['field_id']) . " and " . TUtil::qc('user_id') . " = " . TUtil::qv($signColumnArr['user_id']) . " and " . TUtil::qc('task_id') . " = " . TUtil::qv($signColumnArr['task_id']);
                                            $signData = Yii::app()->db->createCommand($sql)->queryAll();
                                            if (empty($signData))
                                                Yii::app()->db->createCommand()->insert('form_data_countersign', $signColumnArr);
                                            if (Yii::app()->db->getDriverName() == 'oci') { //oracle
                                                $sql = "select " . TUtil::qc('form_data_countersign_id_seq') . ".currval as id from " . TUtil::qc('form_data_countersign');
                                                $command = Yii::app()->db->createCommand($sql)->query()->read();
                                                $newSignInfo[$fieldID] = $command['ID'];
                                            } else {
                                                $newSignInfo[$fieldID] = Yii::app()->db->lastInsertID;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else if ($type === 'dimensionalcode') {
                        $value = serialize($value);
                    }
                }

                if (is_array($value) && $type == 'listview') {//列表数据
                    $listColumnArr = array();
                    $listColumnArr['form_data_id'] = $dataID;
                    foreach ($value as $listkey => $listData) {
                        $value = $listkey;
                        $listTableName = 'form_grid_data_' . $listkey;
                        if (!empty($listData) && is_array($listData)) {
                            //验证是否存在列表数据表
                            if (!TFormUtil::whetherTableExist('form_grid_data_', $listkey)) {
                                $fieldModel = FormHtmlField::model()->findByPk($listkey);
                                if ($fieldModel) {
                                    TFormUtil::createListTable($fieldModel);
                                }
                            } else {//暂时做成全删全插
                                Yii::app()->db->createCommand()->delete($listTableName, TUtil::qc("form_data_id") . '=:form_data_id', array(':form_data_id' => $dataID));
                            }
                            foreach ($listData as $trkey => $trInfo) {
                                if ($trkey != 'total' && is_array($trInfo)) {
                                    foreach ($trInfo as $keyfield => $fieldValue) {
                                        //不存在 增加column
                                        $tableSchema = TFormUtil::whetherTableExist('form_grid_data_', $value);
                                        if (!TFormUtil::whetherColumnExist($tableSchema, $keyfield)) {
                                            TDbUtil::query('ALTER TABLE ' . "`" . $listTableName . "`" . ' ADD COLUMN ' . "`" . $keyfield . "`" . ' text;');
                                        }
                                        $listColumnArr[$keyfield] = $fieldValue['value'];
                                    }
                                    Yii::app()->db->createCommand()->insert($listTableName, $listColumnArr);
                                }
                            }
                        }
                    }
                }
                if ($type == 'tcount' && $value) {
                    $value = $this->generateExpression(mb_substr($fieldID, 5), $formData);
                }
                $columnArr[$fieldID] = $value;
            }
            try {
                if (!empty($columnArr)) {
                    Yii::app()->db->createCommand()->update($table, $columnArr, TUtil::qc("id") . '=:id', array(':id' => $dataID));
                    $this->autoSync($formID, $newSignInfo, $accessFields);
                }
                $newSignInfo['result'] = 'OK';
                return $newSignInfo;
            } catch (Exception $exc) {
                Yii::app()->core->log($exc->getMessage());
                return false;
            }
            return false;
        } else {
            return false;
        }
    }

    public function autoSync($formID, $signInfo, $accessFields) {
        if ($this->owner instanceof Document) {
            $typeId = $this->owner->attributes['type_id'];
        } else if ($this->owner instanceof WfRtApplication) {
            $typeId = $this->owner->attributes['application_id'];
        }
        $formType = intval($this->getFormType($formID, $typeId));
        if (Yii::app()->params['project'] == 'tjhbj') {
            $this->specialSync($formID, $this->owner->id);
        }
        if ($formType === 0) {//子表单
            if (is_array($accessFields) && !empty($accessFields)) {
                $subFormData = CJSON::decode($this->getFormJsonData($formID));
                $model = FormMap::model()->find(TUtil::qc("model") . '=:model AND ' . TUtil::qc("pk") . '=:pk AND ' . TUtil::qc("type") . '=1', array(':model' => 'doc_type', ':pk' => $typeId));
                if ($model) {
                    $mainFormId = $model->form_id;
                    $postfix = Form::getIdByFormId($mainFormId);
                    $mainTable = $this->prefix . $postfix;
                    $fieldInfo = FormHtmlField::getFields($mainFormId);
                    $dataID = $this->getMapInfo($mainFormId);
                    foreach ($fieldInfo as $fieldAttrs) {
                        $mainId[$fieldAttrs['field_name']] = $fieldAttrs['id'];
                    }
                    foreach ($subFormData as $dataInfo) {
                        $id = $dataInfo['id'];
                        if (in_array($id, $accessFields) && array_key_exists($dataInfo['name'], $mainId)) {
                            $type = $dataInfo['type'];
                            $value = $dataInfo['value'];
                            $name = $dataInfo['name'];
                            $mainField = 'data_' . $mainId[$name];
                            $mainColumn[$mainField] = $value;
                            if ($type == 'countersign') {
                                $newSignData = $this->getNewSignData($signInfo['data_' . $dataInfo['id']]);
                                $mainColumn[$mainField] = $mainId[$name];
                                if (is_array($newSignData) && !empty($newSignData)) {
                                    $newSignData['field_id'] = $mainId[$name];
                                    $newSignData['form_data_id'] = $dataID;
                                    Yii::app()->db->createCommand()->insert('form_data_countersign', $newSignData);
                                }
                            }
                            if ($type == 'listview') {
                                $mainColumn[$mainField] = $mainId[$name];
                            }
                        }
                    }
                    if (!empty($mainColumn)) {
                        if ($dataID !== null) {
                            Yii::app()->db->createCommand()->update($mainTable, $mainColumn, TUtil::qc("id") . '=:id', array(':id' => $dataID));
                        }
                    }
                }
            }
        }
    }

    /**
     * 天津收文主子表单数据映射
     */
    public function specialSync($formID, $pk) {
        if ($formID === '45ee1528-a5b1-4991-116b-3de40ed417f0') {
            $dataID = $this->getMapInfo($formID);
            $sql = "select " . TUtil::qc('signuser') . "," . TUtil::qc('signtime') . " from form_data_countersign where " . TUtil::qc('form_data_id') . " = " . TUtil::qv($dataID) . " and field_id in ('53a781e3f20d8','53a781e3f3836','53a781e400db1')";
            $signData = Yii::app()->db->createCommand($sql)->queryAll();
            $subFormInfo = FormDataMap::model()->find(TUtil::qc("pk") . "=:pk and " . TUtil::qc("model") . "='document' and " . TUtil::qc("form_id") . "!='45ee1528-a5b1-4991-116b-3de40ed417f0'", array(':pk' => $pk));
            if ($subFormInfo) {
                $subFormId = Form::getIdByFormId($subFormInfo['form_id']);
                $subFormDataId = $subFormInfo['form_data_id'];
                $subTable = 'form_data_' . $subFormId;
                if (!empty($signData)) {
                    $str = '';
                    foreach ($signData as $item) {
                        $str .= $item['signuser'] . '   ' . $item['signtime'] . "\n";
                    }
                    Yii::app()->db->createCommand()->update($subTable, array('data_53a781dea2a2d' => $str), 'id=:id', array(':id' => $subFormDataId));
                }
            }
        }
    }

    public function getFormFields($formID, $freeAccess = false) {
        if (TUtil::tstrpos($formID, ',') !== false) {
            $forms = TUtil::texplode($formID);
            $fields = array();
            foreach ($forms as $form) {
                $models = FormHtmlField::model()->findAll(array(
                    'select' => array('id', 'field_name'),
                    'condition' => TUtil::qc("form_id") . '=:form_id and ' . TUtil::qc("delete_flag") . '=0',
                    'params' => array(':form_id' => $form),
                ));
                foreach ($models as $model) {
                    if ($freeAccess) {
                        $fields[$form][] = [$model->id];
                    } else {
                        $fields[$form][$model->id] = $model->field_name;
                    }
                }
            }
            return $fields;
        } else {
            $list = FormHtmlField::model()->findAll(array(
                'select' => array('id', 'field_name'),
                'condition' => TUtil::qc("form_id") . '=:form_id and ' . TUtil::qc("delete_flag") . '=0',
                'params' => array(':form_id' => $formID),
            ));
            $fieldArray = array();
            if (!empty($list)) {
                foreach ($list as $record) {
                    if ($freeAccess) {
                        $fieldArray[$formID][] = $record->id;
                    } else {
                        $fieldArray[$record->id] = $record->field_name;
                    }
                }
            }
            return $fieldArray;
        }
    }

    public function getMainFormSetting() {
        $this->_init();
        $formid = '';
        $model = FormMap::model()->find(TUtil::qc("model") . '=:model AND ' . TUtil::qc("pk") . '=:pk AND ' . TUtil::qc("type") . '=1', array(':model' => $this->_model, ':pk' => $this->_modelPk));
        if ($model)
            $formid = $model->form_id;
        return $formid;
    }

    public function getSubFormSetting() {
        $this->_init();
        $formids = array();
        $models = FormMap::model()->findAll(array('select' => array('form_id'), 'condition' => TUtil::qc("model") . '=:model AND ' . TUtil::qc("pk") . '=:pk AND ' . TUtil::qc("type") . '=0', 'params' => array(':model' => $this->_model, ':pk' => $this->_modelPk)));
        foreach ($models as $model) {
            $formids[] = $model->form_id;
        }
        return $formids;
    }

    public function saveFormSetting($formId, $main = 1) {
        $this->_init();
        $formmap = new FormMap();
        $formmap->deleteAll(TUtil::qc("model") . '=:model AND ' . TUtil::qc("pk") . '=:pk AND ' . TUtil::qc("type") . '=:type', array(':model' => $this->_model, ':pk' => $this->_modelPk, ':type' => $main));
        if (is_array($formId)) {
            foreach ($formId as $form) {
                $formmap->unsetAttributes();
                $formmap->setIsNewRecord(true);
                $formmap->attributes = array(
                    'form_id' => $form,
                    'model' => $this->_model,
                    'pk' => $this->_modelPk,
                    'type' => $main,
                );
                $formmap->save();
            }
        } elseif (TUtil::tstrpos($formId, ',') !== false) {
            $forms = TUtil::texplode($formId);
            foreach ($forms as $form) {
                $formmap->unsetAttributes();
                $formmap->setIsNewRecord(true);
                $formmap->attributes = array(
                    'form_id' => $form,
                    'model' => $this->_model,
                    'pk' => $this->_modelPk,
                    'type' => $main,
                );
                $formmap->save();
            }
        } else {
            $formmap->unsetAttributes();
            $formmap->setIsNewRecord(true);
            $formmap->attributes = array(
                'form_id' => $formId,
                'model' => $this->_model,
                'pk' => $this->_modelPk,
                'type' => $main,
            );
            $formmap->save();
        }
    }

    public function getFormType($formID, $pk) {
        $mapInfo = FormMap::model()->find(TUtil::qc("model") . '=:model AND ' . TUtil::qc("pk") . '=:pk AND ' . TUtil::qc("form_id") . '=:form_id', array(':model' => 'doc_type', ':pk' => $pk, ':form_id' => $formID));
        return $mapInfo ? $mapInfo->type : $mapInfo;
    }

    public function getFormDataTableField($table) {
        $tableSchema = Yii::app()->db->getSchema()->getTable($table, true);
        $columns = $tableSchema->getColumnNames();
        return $columns;
    }

    /**
     * 表单列表数据查询
     */
    public function listData($pk, $year, $formId, $type = 'view') {
        $columns = array();
        if ($pk == 'none') {
            $criteria = new CDbCriteria();
            $criteria->addCondition('1!=1');
            $dataProvider = new CActiveDataProvider('Document', array('criteria' => $criteria));
        } else {
            $model = DocType::model()->findByPk($pk);
            if ($model) {
                $formId = $formId == 'none' ? $model->mainForm : $formId;
                $postfix = Form::getIdByFormId($formId);
                $exportFieldInfo = $model->getExportField();
                $exportField = $exportFieldInfo['allExportField'][$formId];
                $formField = FormHtmlField::getFieldType($formId, $exportField, 'all');
                $dataIds = $this->getFormDataIdsByYear($formId, $pk, $year);
                if (!empty($exportField) && TFormUtil::whetherTableExist('form_data_', $formId)) {
                    $fields = $where = '';
                    foreach ($exportField as $field) {
                        $formdatafield = 'data_' . $field;
                        $fields .= $formdatafield . ',';
                        $where .= $formdatafield . '!="" or ';
                        $column = array_merge($type == 'view' ? array('header' => $formField['name'][$field]) : array('text' => $formField['name'][$field]), array(
                            'name' => $formdatafield,
                            'value' => $formField['type'][$field] == 'checkbox' ? ('$data["' . $formdatafield . '"] == "checked" ? "是" : "否"') : '$data["' . $formdatafield . '"]',
                            'htmlOptions' => array('style' => 'text-align:center;'),
                            'headerHtmlOptions' => array('style' => 'text-align:center;')
                        ));
                        if ($formField['type'][$field] == 'countersign') {
                            $column['value'] = 'TFormUtil::getExportSignData($data["' . $formdatafield . '"],$data["id"])';
                            $column['type'] = 'raw';
                        }
                        $columns[] = $column;
                    }
                    $where = '(' . mb_substr($where, 0, -3) . ')';
                    if (!empty($dataIds)) {
                        $idstr = implode(',', $dataIds);
                        $where .= ' and id in (' . $idstr . ')';
                    } else {
                        $where .= ' and 1!=1';
                    }
                    $count = Yii::app()->db->createCommand('SELECT COUNT(*) FROM ' . '`form_data_' . $postfix . '` where ' . $where)->queryScalar();
                    $sql = 'SELECT id,' . trim($fields, ',') . '  FROM ' . '`form_data_' . $postfix . '` where ' . $where;
                } else {
                    $count = 0;
                    if (TFormUtil::whetherTableExist('form_data_', $formId)) {
                        $sql = 'SELECT *  FROM ' . '`form_data_' . $postfix . '` where 1!=1';
                    } else {
                        return array('dataProvider' => array(), 'columns' => array());
                        Yii::app()->end();
                    }
                }
            }
            $dataProvider = new CSqlDataProvider($sql, array(
                'totalItemCount' => $count,
                'pagination' => array(
                    'pageSize' => 15,
                ),
            ));
        }
        return array('dataProvider' => $dataProvider, 'columns' => $columns);
    }

    /**
     * 表单列表数据查询
     */
    public function listWorkflowData($pk, $formId, $type = 'view') {
        $columns = array();
        if ($pk == 'none') {
            $criteria = new CDbCriteria();
            $criteria->addCondition('1!=1');
            $dataProvider = new CActiveDataProvider('WfDfApplication', array('criteria' => $criteria));
        } else {
            $model = WfDfApplication::model()->findByPk($pk);
            if ($model) {
                $formId = $formId == 'none' ? $model->mainForm : $formId;
                $postfix = Form::getIdByFormId($formId);
                $exportFieldInfo = $model->getExportField();
                $exportField = $exportFieldInfo['allExportField'][$formId];
                $formField = FormHtmlField::getFieldType($formId, $exportField, 'all');
                if (!empty($exportField) && TFormUtil::whetherTableExist('form_data_', $formId)) {
                    $fields = $where = '';
                    foreach ($exportField as $field) {
                        $formdatafield = 'data_' . $field;
                        $fields .= $formdatafield . ',';
                        $where .= $formdatafield . '!="" or ';
                        $column = array_merge($type == 'view' ? array('header' => $formField['name'][$field]) : array('text' => $formField['name'][$field]), array(
                            'name' => $formdatafield,
                            'value' => $formField['type'][$field] == 'checkbox' ? ('$data["' . $formdatafield . '"] == "checked" ? "是" : "否"') : '$data["' . $formdatafield . '"]',
                            'htmlOptions' => array('style' => 'text-align:center;'),
                            'headerHtmlOptions' => array('style' => 'text-align:center;')
                        ));
                        if ($formField['type'][$field] == 'countersign') {
                            $column['value'] = 'TFormUtil::getExportSignData($data["' . $formdatafield . '"],$data["id"])';
                            $column['type'] = 'raw';
                        }
                        $columns[] = $column;
                    }
                    $where = '(' . mb_substr($where, 0, -3) . ')';
                    $count = Yii::app()->db->createCommand('SELECT COUNT(*) FROM ' . '`form_data_' . $postfix . '` where ' . $where)->queryScalar();
                    $sql = 'SELECT id,' . trim($fields, ',') . '  FROM ' . '`form_data_' . $postfix . '` where ' . $where;
                } else {
                    $count = 0;
                    if (TFormUtil::whetherTableExist('form_data_', $formId)) {
                        $sql = 'SELECT *  FROM ' . '`form_data_' . $postfix . '` where 1!=1';
                    } else {
                        return array('dataProvider' => array(), 'columns' => array());
                        Yii::app()->end();
                    }
                }
            }
            $dataProvider = new CSqlDataProvider($sql, array(
                'totalItemCount' => $count,
                'pagination' => array(
                    'pageSize' => 15,
                ),
            ));
        }
        return array('dataProvider' => $dataProvider, 'columns' => $columns);
    }

    public function getFormDataIdsByYear($formId, $typeId, $year) {
        $criteria = new CDbCriteria();
        $criteria->addCondition('type_id="' . $typeId . '"');
        if ($year != 'none') {
            $criteria->addCondition('FROM_UNIXTIME(create_time,"%Y")="' . $year . '"');
        }
        $criteria->select = 'id';
        $models = Document::model()->findAll($criteria);
        $pks = array();
        if (!empty($models)) {
            foreach ($models as $model) {
                $pks[] = $model['id'];
            }
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition(TUtil::qc('pk'), $pks);
        $criteria->addCondition(TUtil::qc('model') . '=' . TUtil::qv("document") . ' and ' . TUtil::qc("form_id") . '=' . TUtil::qv($formId));
        $criteria->select = 'form_data_id';
        $formDataMaps = FormDataMap::model()->findAll($criteria);
        $dataIds = array();
        if (!empty($formDataMaps)) {
            foreach ($formDataMaps as $formDataMap) {
                $dataIds[] = $formDataMap['form_data_id'];
            }
        }
        return $dataIds;
    }

    public function getVariableVal($formID, $variables, $variableValues = null, $order = null) {
        $formFieldData = $this->getFormFieldData($formID, 'name');
        $return = array();
        $positionName = '';
        $roleName = '';
        if (Yii::app()->params['project'] == 'cqhbj') {
            $filterOrder = Yii::app()->params['wf_filter_node'];
            if (in_array($order, $filterOrder)) {
                $variableValues = unserialize($variableValues);
                foreach ($variableValues as $key => $variableValue) {
                    $nt = $variableValue['name'];
                    if ($nt == '@上一转交步骤') {
                        $order = $variableValue['value'];
                    }
                }
            }
        }
        if (!empty($variables)) {
            foreach ($variables as $variable) {
                $name = $variable['name'];
                if ($name === '@当前登录用户') {
                    $return[] = array('name' => $name, 'value' => Yii::app()->user->user_name);
                } else if ($name === '@用户主组织') {
                    $return[] = array('name' => $name, 'value' => Org::getNameById(Yii::app()->user->org['main']));
                } else if ($name === '@用户辅助组织') {
                    $return[] = array('name' => $name, 'value' => implode(',', Org::getNameById(Yii::app()->user->org['other'])));
                } else if ($name === '@用户角色') {
                    $roles = Yii::app()->user->role;
                    if (!empty($roles)) {
                        foreach ($roles as $role) {
                            $roleName .= $role['name'] . ',';
                        }
                    }
                    $return[] = array('name' => $name, 'value' => trim($roleName, ','));
                } else if ($name === '@用户岗位') {
                    $positions = Yii::app()->user->position;
                    if (!empty($positions)) {
                        foreach ($positions as $position) {
                            $positionName .= $position['name'] . ',';
                        }
                    }
                    $return[] = array('name' => $name, 'value' => trim($positionName, ','));
                } else if ($name === '@上一转交步骤') {
                    $return[] = array('name' => $name, 'value' => $order);
                } else {
                    $value = '';
                    if (isset($formFieldData[$name])) {
                        if (is_array($formFieldData[$name])) {
                            foreach ($formFieldData[$name] as $signcontent) {
                                if ($signcontent['user'] == Yii::app()->user->user_name) {
                                    $value = $signcontent['content'];
                                }
                            }
                        } else {
                            $value = $formFieldData[$name];
                        }
                    }
                    $return[] = array('name' => $name, 'value' => $value);
                }
            }
        }
        return $return;
    }

}

?>
