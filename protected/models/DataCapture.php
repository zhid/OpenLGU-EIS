<?php

Class DataCapture extends CFormModel
{
	public $field;
	public $field_name;
	public $data_type;

	public function rules()
	{
		return array (
			array('field', 'required'),
			array('field', 'checkdatatype'),
		);
	}
	
	public function checkdatatype()
	{
		if($this->data_type == 'double precision' || $this->data_type == 'bigint')
		{
			if(is_numeric($this->field))
			{
				
			}
			else
			{
				$this->addError('field', 'Input must be a number');
			}
		}
	}
}