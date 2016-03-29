<?php
class EClobBehavior extends CActiveRecordBehavior
{
    public $clobAttributes = array(); // array or comma separated attributes

    public function afterFind($event)
    {
        if(!empty($this->clobAttributes))
        {
            $model = $this->owner;
            if(is_string($this->clobAttributes))
                $this->clobAttributes = explode(',', $this->clobAttributes);
            foreach($this->clobAttributes as $attr)
            {
                $attr = trim($attr);
                if(is_resource($model->{$attr}))
                    $model->{$attr} = stream_get_contents($model->{$attr});
            }
        }
    }
}
