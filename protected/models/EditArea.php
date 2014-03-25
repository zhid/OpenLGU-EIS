<?php

Class EditArea extends CFormModel
{
	public $area_id;
	public $area_name;
	public $area_logo;
	public $managing_office;
	public $officer_in_charge;
	public $visible;
	public $service_area;

	public function rules()
	{
		return array (
			array('area_name', 'required'),
			array('area_name', 'checkareaname'),
			array('managing_office', 'required'),
			array('officer_in_charge', 'required'),
			array('service_area', 'required'),
			array('area_logo', 'required')
		);
	}
	
	public function checkareaname()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'area_name';
		$criteria->condition = 'area_name=:area_name AND area_id<>:area_id';
		$criteria->params = array(':area_name'=>$this->area_name, ':area_id'=>$this->area_id);
		$count = Area::model()->count($criteria);
		
		if($count > 0)
		{
			$this->addError('area_name', 'Area name already exist in the database');
		}
	}
}