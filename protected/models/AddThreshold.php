<?php

Class AddThreshold extends CFormModel
{
	public $measure_id;
	public $column_id;
	public $lowthreshold;
	public $highthreshold;
	public $lowthreshold_operator;
	public $highthreshold_operator;
	public $threshold_type;

	public function rules()
	{
		return array (
			array('measure_id, column_id, lowthreshold_operator, highthreshold_operator, threshold_type', 'required'),
			array('lowthreshold', 'required'),
			array('lowthreshold, highthreshold', 'numerical'),
		);
	}
}