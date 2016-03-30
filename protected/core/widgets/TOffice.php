<?php

Yii::import('core.widgets.TWidget');

class TOffice extends TWidget {

    public $params = array();
    public $scriptFile = '/office/ntko.js';
    public $op = 7;
    public $fileName;
    public $fileExtName;
    
    const NTKO_VERSION = '5,0,2,2';
    const NTKO_PRODUCT_CAPTION = 'Office Anywhere';
    const NTKO_PRODUCT_KEY = '460655BF84C22ADA846B8AC7E4B3089882E368B3';
    const NTKO_NAME = 'OfficeControl.cab';
    const NTKO_CLSID = '01DFB4B4-0E07-4e3f-8B7A-98FD6BFF153F';
    public $version;
    public $productCaption;
    public $productKey;
    public $name;
    public $clsid;
    public function init() {
        parent::init();
        if (isset($this->params['op'])) {
            $this->op = $this->params['op'];
        }
        if (isset($this->params['fileName'])) {
            $this->fileName = $this->params['fileName'];
        }
        if(isset($this->params['fileExtName'])){
            $this->fileExtName = $this->params['fileExtName'];
        }
        
        $this->version = self::NTKO_VERSION;
        $this->productCaption = self::NTKO_PRODUCT_CAPTION ;
        $this->productKey = self::NTKO_PRODUCT_KEY;
        $this->name = self::NTKO_NAME;
        $this->clsid = self::NTKO_CLSID;
        
        $params = SysParams::getParams(array('ntko_standalone', 'ntko_name', 'ntko_clsid', 'ntko_version', 'ntko_product_caption', 'ntko_product_key'));
        if($params['ntko_standalone'] 
                && $params['ntko_name'] 
                && $params['ntko_version'] 
                && $params['ntko_product_caption'] 
                && $params['ntko_product_key']){
            $this->version = $params['ntko_version'];
            $this->productCaption = $params['ntko_product_caption'] ;
            $this->productKey = $params['ntko_product_key'];
            $this->name = $params['ntko_name'];
            if($params['ntko_clsid']) {
                $this->clsid = $params['ntko_clsid'];
            }
        }
        if(!Yii::app()->storage->isFileExists(Yii::app()->basePath."/../static/activex/". $this->name)) {
            throw new Exception("控件配置错误，请检查系统参数设置！",404);
        }
    }

    public function run() {//TANGER_OCX
        echo "<object id='TANGER_OCX' classid='clsid:".$this->clsid."'
                    codebase='".Yii::app()->core->staticUrl."/activex/". $this->name ."#version=". $this->version ."' width='100%' height='580px'>";
        if ($this->op == 4 || $this->op == 7) {
            echo "<param name='IsNoCopy' value='0'>
                  <param name='FileSave' value='-1'>
                  <param name='FileSaveAs' value='-1'>";
        } else {
            echo "<param name='IsNoCopy' value='-1'>
                  <param name='FileSave' value='0'>
                  <param name='FileSaveAs' value='0'>";
        }
        echo "<param name='Caption' value='Office 文档在线编辑'>
              <param name='BorderStyle' value='3'>
              <param name='BorderColor' value='14402205'>
              <param name='Titlebar' value='0'>
              <param name='TitlebarColor' value='14402205'>
              <param name='TitlebarTextColor' value='0'>
              <param name='Menubar' value='-1'>
              <param name='MenubarColor' value='14402205'>
              <param name='MenuBarStyle' value='2'>
              <param name='MenuButtonFrameColor' value='102205'>
              <param name='ToolBars' value='-1'>
              <param name='IsShowToolMenu' value='-1'>
              <param name='IsHiddenOpenURL' value='0'>
              <param name='IsUseUTF8URL' value='1'>
              <param name='MakerCaption' value='中国兵器工业信息中心通达科技'>
              <param name='MakerKey' value='EC38E00341678B7549B46F19D4CAF4D89866B164'>
              <param name='ProductCaption' value='". $this->productCaption ."'>
              <param name='ProductKey' value='". $this->productKey ."'>";
        $this->widget('core.widgets.TMessageBox', array(
            'icon' => 'icon-bubble-2',
            'type' => 'danger',
            'title' => '系统提示',
            'content' => '不能装载文档控件，请设置好IE安全级别为中或中低，不支持非IE内核的浏览器。',
        ));
        echo "</object>";
        echo "<div id='OC_LOG' align='center' style='display:none;'></div>
              <div id='OC_HISTORY' align='center' style='display:none;'></div>";
        echo '<script language="javascript" for="TANGER_OCX" event=OnDocumentClosed()>
                    NTKO.onDocumentClosed();
              </script>
              <script>
                    var TANGER_OCX_str;
                    var TANGER_OCX_obj;
               </script>
               <script language="javascript" for="TANGER_OCX" event=OnDocumentOpened(TANGER_OCX_str,TANGER_OCX_obj)>
                    NTKO.onDocumentOpened(TANGER_OCX_str,TANGER_OCX_obj);
                    NTKO.setReadOnly(false);';
        if (stristr($this->fileName, ".doc") || stristr($this->fileName, ".xls") || stristr($this->fileName, ".xlsx")) {
//            echo 'NTKO.showRevisions(true)';
            if ($this->op != 4 && $this->op != 7)
                echo 'NTKO.setReadOnly(true)';
        }
        if($this->op == 7)
            echo 'NTKO.setReadOnly(true)';
        if ($this->op == 5) {
            echo 'NTKO.filePrint(false)';
            echo 'NTKO.filePrintToolbar(false)';
        }

        echo '</script>';
    }
}
?>