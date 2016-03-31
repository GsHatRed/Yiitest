<?php
class uniqueMulti extends CValidator
{
	private $allowEmpty = false;
	public function setAllowEmpty($value) {$this->allowEmpty = $value;}
	public function getAllowEmpty() {return $this->allowEmpty;}

	private $caseSensitive = false;
	public function setCaseSensitive($value) {$this->caseSensitive = $value;}
	public function getCaseSensitive() {return $this->caseSensitive;}



	protected function validateAttribute($object,$attribute)
	{
		$criteria=array('condition'=>'');
		if(false !== strpos($attribute, "+"))
		{
			$attributes = explode("+", $attribute);
		}
		else
		{
			$attributes = array($attribute);
		}
		$where = "";
		foreach($attributes as $key => $attribute)
		{
			$where .= $attribute . "='" . $object->$attribute ."'";
			if(array_key_exists($key+1, $attributes)) {
				$where .= ' AND ';
			}
		}
		$exists = Contact::model()->find($where);
		if(!empty($exists)) {
			$this->addError($object, $attributes[0], $this->message);
		}
	}
}
?>
