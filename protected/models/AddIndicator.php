<?php

Class AddIndicator extends CFormModel
{
	public $area_id;
	public $column_id;
	public $threshold;
	public $indicator_operator;
	public $indicator_type;

	public function rules()
	{
		return array (
			array('area_id, column_id, indicator_operator, indicator_type', 'required'),
			array('threshold', 'required'),
		);
	}
}