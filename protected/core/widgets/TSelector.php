<?php
/**
 * TSelector class file.
 *
 */

Yii::import('core.widgets.TWidget');
/**
 * 选择器组件类
 */
class TSelector extends CInputWidget {

    public $form;

    public $type;

    public $single = false;
    
    public $codeValue = false;

    public $config;

    public $addFunction;

    public $delFunction;

    public $addButtonOptions;

    public $delButtonOptions;

    public $hiddenFieldHtmlOptions = array();

    public $textFieldHtmlOptions = array();

    public $getNameMethod = 'getNameById';
    
    public $controllerId;

    public $showOtherOrg;
    private $_defaultConfig;
    
    public $params = array();
    
    
    public $readOnly = true; //是否显示readOnly属性
    public $getNameByMethod = true;  //是否给name赋值
    public $setName = false; //是否给name赋值
    public $style;
    
    /**
     * 打开类型
     */
    public $openType = 'window';


    public function init() {
        if(!isset($this->type))
            throw new CException('类型不能为空!');
        $this->controllerId = $this->getController()->id;
        if(in_array($this->type,array('user'))) {
//            $this->showOtherOrg = User::isInterfaceUser() && User::isIndependentUser() && !in_array($this->controllerId,Yii::app()->params['filter_models']) ? true : false;
            $this->showOtherOrg = User::isInterfaceUser() && Yii::app()->user->independent && !in_array($this->controllerId,Yii::app()->params['filter_models']) ? true : false;
        }
        if($this->openType == 'window'){
            $this->_defaultConfig = array(
                'user' => array(
                    'modal' => 'user',
                    'class' => 'User',
                    ),
                'org' => array(
                    'modal'=>'org',
                    'class'=>'Org',
                    ),
                'grade' => array(
                    'modal'=>'grade',
                    'class'=>'EduOrg',
                    ),
                'class' => array(
                    'modal'=>'class',
                    'class'=>'EduOrg',
                    ),
                 'student' => array(
                    'modal'=>'student',
                    'class'=>'EduStudents',
                    ),
                 'classstudent' => array(
                    'modal'=>'classstudent',
                    'class'=>'EduStudents',
                    ),
                 'parents' => array(
                    'modal'=>'parents',
                    'class'=>'EduParents',
                    ),
                 'eduorg' => array(
                    'modal'=>'eduorg',
                    'class'=>'EduOrg',
                    ),
                 'teacher' => array(
                    'modal'=>'teacher',
                    'class'=>'EduTeacher',
                    ),
                 'stage' => array(
                    'modal'=>'stage',
                    'class'=>'EduOrg',
                    ),
                 'teacherclass' => array(
                    'modal'=>'teacherclass',
                    'class'=>'EduOrg',
                    ),
                'role' => array(
                    'modal'=>'role',
                    'class'=>'TDbAuthManager'
                    ),
                'position' => array(
                    'modal' => 'position',
                    'class' => 'Position',
                    ),
                'table' => array('modal'=>'table'),
                'form' => array('modal'=>'form'),
                'doctype' => array('modal'=>'doctype','class'=>'DocType'),
                'wfdf' => array('modal'=>'wfdf'),
                'wfdfapp' => array('modal'=>'wfdfapp','class'=>'WfDfApplication'),
                'address' => array(
                    'modal'=>'address',
                    'class'=>'Address'
                 ),
                'wfuser' => array(
                    'modal'=>'wfuser',
                    'class'=>'User',
                ),
                'parase' => array(
                    'modal' => 'parase',
                    'class' => 'UserParase'
                ),
            );
        } else {
            $this->_defaultConfig = array(
                'user' => array(
                    'modal' => $this->single == false ? 'selectuser' : 'selectsingleuser',
                    'class' => 'User',
                    ),
               'grade' => array(
                    'modal'=>$this->single == false ? 'selectgrade' : 'selectsinglegrade',
                    'class'=>'EduOrg',
                    ),
               'class' => array(
                    'modal'=>$this->single == false ? 'selectclass' : 'selectsingleclass',
                    'class'=>'EduOrg',
                    ),
               'student' => array(
                    'modal'=>$this->single == false ? 'selectstudent' : 'selectsinglestudent',
                    'class'=>'EduStudents',
                    ),
               'teacher' => array(
                    'modal'=>$this->single == false ? 'selectteacher' : 'selectsingteacher',
                    'class'=>'EdTeacher',
                    ),
               'classstudent' => array(
                    'modal'=>$this->single == false ? 'selectclassstudent' : 'selectsingleclassstudent',
                    'class'=>'EduStudents',
                    ),
               'parents' => array(
                    'modal'=>$this->single == false ? 'selectparents' : 'selectsingleparents',
                    'class'=>'EduParents',
                    ),
               'eduorg' => array(
                    'modal'=>$this->single == false ? 'selecteduorg' : 'selectsingleeduorg',
                    'class'=>'EduOrg',
                    ),
                'org' => array(
                    'modal'=>$this->single == false ? 'selectorg' : 'selectsingleorg',
                    'class'=>'Org',
                    ),
                 'stage' => array(
                    'modal'=>$this->single == false ? 'selectstage' : 'selectsinglestage',
                    'class'=>'EduOrg',
                    ),
                'teacherclass' => array(
                    'modal'=>$this->single == false ? 'selectteacherclass' : 'selectsingleteacherclass',
                    'class'=>'EduOrg',
                    ),
                'role' => array(
                    'modal'=>'selectrole',
                    'class'=>'TDbAuthManager'
                    ),
                'position' => array(
                    'modal' => 'position',
                    'class' => 'Position',
                    ),
                'table' => array('modal'=>'selecttable'),
                'form' => array('modal'=>'selectform'),
                'doctype' => array('modal'=>'selectdoctype','DocType'),
                'wfdf' => array('modal'=>'selectwfdf'),
                'wfdfapp' => array('modal'=>'selectwfdfapp','class'=>'WfDfApplication'),
                'address' => array(
                    'modal'=>'selectaddress',
                    'class'=>'Address'
                 ),
                'wfuser' => array(
                    'modal'=>'selectwfuser',
                    'class'=>'User',
                ),
                'parase' => array(
                    'modal' => 'selectparase',
                    'class' => 'UserParase'
                ),
            );
        }

        if(!isset($this->config)){
            $this->config = $this->_defaultConfig[$this->type];
        }

        list($name, $id) = $this->resolveNameID();

        if(!isset($this->hiddenFieldHtmlOptions['name'])){
            $this->hiddenFieldHtmlOptions['name'] = $name;
        }
        
        $this->textFieldHtmlOptions['id'] = $id . '_name';
        
        if(!isset($this->textFieldHtmlOptions['value'])){
            if($this->hasModel()){
                $this->textFieldHtmlOptions['value'] = $this->model->{$this->attribute};
            } else {
                $this->textFieldHtmlOptions['value'] = $this->value;
            }
        }

        if($this->codeValue){
            $this->getNameMethod = 'getNameByNo';
        }

        if(isset($this->textFieldHtmlOptions['value']) && $this->getNameByMethod && method_exists($this->config['class'], $this->getNameMethod)){
            $class = $this->config['class'];
            $this->textFieldHtmlOptions['value'] = call_user_func(array($class, $this->getNameMethod),$this->textFieldHtmlOptions['value']);
        }

        if(!isset($this->textFieldHtmlOptions['style'])){
            $this->textFieldHtmlOptions['style'] = 'margin-bottom:0;';
        } else{
            $this->textFieldHtmlOptions['style'] .= 'margin-bottom:0;';
        }
        if(isset($this->style)){
            $this->textFieldHtmlOptions['style'] .=$this->style;
        }
        if($this->readOnly)
            $this->textFieldHtmlOptions['readOnly'] = 'readOnly';
             
        if($this->setName)
            $this->textFieldHtmlOptions['name'] = $id . '_name';
        else
            $this->textFieldHtmlOptions['name'] = '';
        if(!$this->addFunction){
            if($this->openType == 'window'){
                $url = Yii::app()->createUrl('/portal/selector/'.$this->config['modal'], array_merge(array('fid'=>$id, 'fname'=>$id.'_name', 'single'=>$this->single, 'codeValue'=>$this->codeValue, 'controllerId'=>$this->controllerId,'showOtherOrg'=>$this->showOtherOrg), $this->params));
                $this->addFunction = 'selectoropen("'.$url.'")';
            } else {
                $this->addFunction = 'vk_' . $this->config['modal'] . '("' . $id . '","' . $id . '_name' . '","' . $this->config['modal'] . '", '.($this->single ? 1 : 0).');';
            }
            
        }

        if(!$this->delFunction){
            $this->delFunction = 'selectorclear("' . $id . '","' . $id . '_name' . '")';
        }
        if(!$this->addButtonOptions['label']){
           $this->addButtonOptions['label'] = "选择";
        }
        if(!$this->addButtonOptions['size']){
           $this->addButtonOptions['size'] = "mini";
        }
        if(!$this->addButtonOptions['htmlOptions']){
           $this->addButtonOptions['htmlOptions'] = array('style' => 'vertical-align:bottom;margin-left:3px;');
        }
        if(!$this->addButtonOptions['htmlOptions']['onclick']){
           $this->addButtonOptions['htmlOptions']['onclick'] = $this->addFunction;
        }
       if(!$this->delButtonOptions['label']){
           $this->delButtonOptions['label'] = "清空";
        }
        if(!$this->delButtonOptions['size']){
           $this->delButtonOptions['size'] = "mini";
        }
        if(!$this->delButtonOptions['htmlOptions']){
           $this->delButtonOptions['htmlOptions'] = array('style' => 'vertical-align:bottom;margin-left:3px;');
        }
         if(!$this->delButtonOptions['htmlOptions']['onclick']){
           $this->delButtonOptions['htmlOptions']['onclick'] = $this->delFunction;
        }
        
//        if(!$this->addButtonOptions){
//            $this->addButtonOptions = array(
//                'label' => '选择',
//                'size' => 'mini',
//                'htmlOptions'=>array(
//                    'style' => 'vertical-align:bottom;',
//                    'onclick' => $this->addFunction,
//                )
//            );
//        }
//        if(!$this->delButtonOptions){
//            $this->delButtonOptions = array(
//                'label' => '清空',
//                'size' => 'mini',
//                'htmlOptions'=>array(
//                    'style' => 'vertical-align:bottom;margin-left:3px;',
//                    'onclick' => $this->delFunction,
//                )
//            );
//        }

    }

    public function run() {

        $this->registerScript();
        
        list($name, $id) = $this->resolveNameID();
        $textWidget = 'textArea';
        $activeTextWidget = 'activeTextArea';
        if($this->single == true) {
            $textWidget = 'textField';
            $activeTextWidget = 'activeTextField';
        }
        if($this->hasModel()){
            if($this->form){
                echo $this->form->hiddenField($this->model, $this->attribute, $this->hiddenFieldHtmlOptions);
                echo $this->form->{$textWidget}($this->model, $this->attribute, $this->textFieldHtmlOptions);
            } else {
                echo CHtml::activeHiddenField($this->model, $this->attribute, $this->hiddenFieldHtmlOptions);
                echo CHtml::$activeTextWidget($this->model, $this->attribute, $this->textFieldHtmlOptions);
            }
        } else {
            echo CHtml::hiddenField($name, $this->value, $this->hiddenFieldHtmlOptions);
            echo CHtml::$textWidget($this->textFieldHtmlOptions['name']!='' ? $this->textFieldHtmlOptions['name'] : '', $this->textFieldHtmlOptions['value'], $this->textFieldHtmlOptions);
        }

        $this->widget('bootstrap.widgets.TbButton', $this->addButtonOptions);
        
        $this->widget('bootstrap.widgets.TbButton', $this->delButtonOptions);

    }
    
    public function registerScript(){
        $cs = Yii::app()->clientScript;
        
        $js = <<<EOD
            var winType = 'modal';
            if (window.ActiveXObject){
                var ua = navigator.userAgent.toLowerCase();
                var ieVersion = ua.match(/msie ([\d.]+)/)[1];
                if(ieVersion === '8.0'){
                    winType = '';
                }
            }
            function selectoropen(url){
                TUtil.openUrl(url, winType, "selectorwindow", "600", "400")
            }
                
            function selectorclear(fid, fname){
                $("#"+fid) && $("#"+fid).val("");
                $("#"+fname) && $("#"+fname).val("");
            }
EOD;
        $cs->registerScript('selector-clear', $js, CClientScript::POS_HEAD);
        
    }

}
