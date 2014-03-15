<?php

Class AddColumn extends CFormModel
{
	public $measure_id;
	public $column_name;
	public $column_data_type;

	public function rules()
	{
		return array (
			array('column_name, column_data_type', 'required'),
			array('column_name', 'checkcolumnname'),
		);
	}
	
	public function checkcolumnname()
	{
		$criteria = new CDbCriteria();
		$criteria->select = '*';
		$criteria->condition = 'column_name=:column_name';
		$criteria->params = array(':column_name'=>$this->column_name);
		$count = ColumnDimension::model()->count($criteria);
		
		if($count > 0)
		{
			$this->addError('column_name', 'Column name already exist in the database');
		}
	}
}