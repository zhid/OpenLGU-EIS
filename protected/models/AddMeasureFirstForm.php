<?php

Class AddMeasureFirstForm extends CFormModel
{
	//number of rows and columns in a measure
	public $number_of_rows;
	public $number_of_columns;
	
	//measure table attributes
	public $measure_name;
	public $description;

	public function rules()
	{
		return array (
			array('number_of_rows', 'numerical'),
			array('number_of_columns', 'numerical'),
			array('number_of_rows, number_of_columns', 'required'),
			array('measure_name', 'required'),
			array('description', 'required'),
			array('measure_name', 'checkmeasurename'),
		);
	}
	
	public function checkmeasurename()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'measure_name';
		$criteria->condition = 'measure_name=:measure_name';
		$criteria->params = array(':measure_name'=>$this->measure_name);
		$count = Measure::model()->count($criteria);
		
		if($count > 0)
		{
			$this->addError('measure_name', 'Measure name already exist in the database');
		}
	}
}