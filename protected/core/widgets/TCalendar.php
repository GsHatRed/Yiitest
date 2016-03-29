<?php

/**
 * TCalendar class file.
 *
 */
Yii::import('core.widgets.TWidget');

/**
 * TCalendar组件包装类.
 *
 * <pre>
 *   $this->widget('core.widgets.TCalendar', array(
 *       "options" => array(
 *           "elementId" => "diary_body",
 *           "elementUrl" => $this->createUrl("/diary/default",array("date"=>"parm")),
 *       ),
 *       "htmlOptions" => array(
 *           "id"=>"calendar"
 *       )
 *     ));
 * </pre>
 *     注意：若options属性只是赋值了elementId,点击日历上日期的时候，id为elementId的对象就被赋予了当前点击的日期，格式是yy-mm-dd,如2013-8-19；        *             若options属性只是赋值了elementUrl,点击日历上日期的时候，有一个参数值date为当前点击日期的页面；
 *          若elementId和elementUrl都存在，此时改变id为elementId的src值为elementUrl的值。
 *
 */
class TCalendar extends TWidget {

    /**
     * @var array the options for the Bootstrap JavaScript plugin.
     */
    public $options = array();

    /**
     * @var array the options for the Bootstrap JavaScript plugin.
     */
    public $htmlOptions = array();

    public $cssFile="calendar.table.css";
    /**
     * ### .init()
     *
     * Initializes the widget.
     */
    public function init() {
        $classes = array('calendar', 'table-condensed');

        if ($this->htmlOptions['id'] == null)
            $this->htmlOptions['id'] = $this->getId();

        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->htmlOptions['class']))
                $this->htmlOptions['class'] .= ' ' . $classes;
            else
                $this->htmlOptions['class'] = $classes;
        }
        parent::init();
    }

    /**
     * ### .run()
     *
     * Runs the widget.
     */
    public function run() {

        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $id = !empty($this->htmlOptions['id']) ? $this->htmlOptions['id'] : $this->getId();
        $elementId = !empty($this->options['elementId']) ? $this->options['elementId'] : 'null';
        $elementUrl = !empty($this->options['elementUrl']) ? $this->options['elementUrl'] : 'null';
        echo CHtml::openTag('div', $this->htmlOptions);
        echo CHtml::openTag('table');
        echo '<thead><tr class="head"><th id="prev" style="visibility: visible;"><i class="icon-arrow-left"></i></th><th colspan="5" class="switch" style="text-align:center;">' . date("Y") . "年" . date("n") . "月" . '</th><th id="next" style="visibility: visible;"><i class="icon-arrow-right-2"></i></th></thead>';
        echo '<tbody></tbody>';
        echo CHtml::closeTag('table');
        echo CHtml::closeTag('div');
        ob_start();
        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), ob_get_clean() . ';');
        if (isset($_GET['date'])) {
            $year = date("Y", strtotime($_GET['date']));
            $month = date("n", strtotime($_GET['date']));
            $day = date("j", strtotime($_GET['date']));
        } else {
            $year = date("Y");
            $month = date("n");
            $day = date("j");
        }

        $this->registerJs($id, $year, $month, $day, $elementId, $elementUrl);
    }

 

    /**
     * ### .registerClientScript()
     *
     * Registers required client script for bootstrap datepicker. It is not used through bootstrap->registerPlugin
     * in order to attach events if any
     */
    protected function registerJs($id, $year, $month, $day, $elementId, $elementUrl) {

        $js = <<<EOD
       var year  = {$year};
       var month  = {$month};
       var day = {$day};
$(document).ready(function(){
    function CreateCalTr(num){
      $("#{$id} tbody").html('<th class="dow">日</td><th class="dow">一</th><th class="dow">二</th><th class="dow">三</th><th class="dow">四</th><th class="dow">五</th><th class="dow">六</th>');
      var html = '';
      for(var i=0; i<num; i++)
      {
         html += '<tr>';
         for(var j=0; j<7; j++)
            html += '<td id="cal_td_' + (i*7+j) + '" class="day active"></td>';
         html += '</tr>';
      }
      $("#{$id} tbody").append(html);
   }
     function InitCalTable()
   {
      var start_id = (new Date(year, month-1, 1)).getDay();
      var end_id = start_id + (new Date(year, month, 0)).getDate();
      if(end_id>35)      
         CreateCalTr(6);
      else
      	 CreateCalTr(5);
      $('td.active',   $("#{$id}")).removeClass('active');
      for(var i=0; i<5; i++)
      {
         for(var j=0; j<7; j++)
         {
            $('#cal_td_'+(i*7+j)).html('');
            $('#cal_td_'+(i*7+j)).unbind('click mouseenter mouseleave');
         }
      }
                  
      for(var i=start_id; i < end_id; i++)
      {
         var td = $('#cal_td_'+i);
         td.html(i-start_id+1);
         td.click(function(){
            day = $(this).html();
            $('td.active', $("#{$id}")).removeClass('active');
            $(this).addClass('active');
            if(("{$elementUrl}" == "null") && ("{$elementId}" != "null")){
              $("#{$elementId}").val(year+'-'+month+'-'+day);
            };
            if(("{$elementUrl}" != "null") && ("{$elementId}" == "null")){
              location.href = "{$elementUrl}".replace("parm",year+'-'+month+'-'+day)
            };
            if(("{$elementUrl}" != "null") && ("{$elementId}" != "null")){
              $("#{$elementId}").attr("src","{$elementUrl}".replace("parm",year+'-'+month+'-'+day));
             }
         });
         if(i-start_id+1 == day)
            td.addClass('active');
      }
   }
   
   function SetMonth(diff)
   {
      month = month + diff;
      if(month > 12)
      {
         month = month - 12;
         year++;
      }
      else if(month <= 0)
      {
         month = 12;
         year--;
      }
      $('.switch').html(year + '年' + month + '月');
      InitCalTable();
   }
 $(document).ready(function(){
      InitCalTable();
      $('#{$id} #prev').click(function(){SetMonth(-1);});
      $('#{$id} #next').click(function(){SetMonth(1);});
   });

   })
   
EOD;
        Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $this->getId(), $js);
    }

}
