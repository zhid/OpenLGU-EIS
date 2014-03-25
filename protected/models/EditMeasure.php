<?php

Class EditMeasure extends CFormModel
{
	public $measure_id;
	public $measure_name;
	public $description;

	public function rules()
	{
		return array (
			array('measure_name, description', 'required'),
			array('measure_name', 'checkmeasurename'),
		);
	}
	
	public function checkmeasurename()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'measure_name';
		$criteria->condition = 'measure_name=:measure_name AND measure_id<>:measure_id';
		$criteria->params = array(':measure_name'=>$this->measure_name, ':measure_id'=>$this->measure_id);
		$count = Measure::model()->count($criteria);
		
		if($count > 0)
		{
			$this->addError('measure_name', 'Measure name already exist in the database');
		}
	}
}