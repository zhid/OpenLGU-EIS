<?php

Class EditArea extends CFormModel
{
	public $area_id;
	public $area_name;
	public $area_logo;
	public $managing_office;
	public $officer_in_charge;
	public $visible;

	public function rules()
	{
		return array (
			array('area_name', 'required'),
			array('area_name', 'checkareaname'),
			array('managing_office', 'required'),
			array('officer_in_charge', 'required'),
			array('area_logo', 'checklogotype'),
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
	
	public function checklogotype()
	{
		if($this->area_logo != NULL)
		{
			$ext = $this->area_logo->getExtensionName();
		
			if($ext != 'jpeg' && $ext != 'png' && $ext != 'gif')
			{
				$this->addError('area_logo', 'Only files with these extensions are allowed: png, jpg, gif.');
			}
		}
	}
}