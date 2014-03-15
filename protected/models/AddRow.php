<?php

Class AddRow extends CFormModel
{
	public $measure_id;
	public $row_name;
	public $row_data_type;

	public function rules()
	{
		return array (
			array('row_name, row_data_type', 'required'),
			array('row_name', 'checkrowname'),
		);
	}
	
	public function checkrowname()
	{
		$criteria = new CDbCriteria();
		$criteria->select = '*';
		$criteria->condition = 'row_name=:row_name';
		$criteria->params = array(':row_name'=>$this->row_name);
		$count = RowDimension::model()->count($criteria);
		
		if($count > 0)
		{
			$this->addError('row_name', 'Row name already exist in the database');
		}
	}
}