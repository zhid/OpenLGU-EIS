<?php

Class AddArea extends CFormModel
{
	public $area_name;
	public $area_logo;
	public $color_rating;
	public $managing_office;
	public $officer_in_charge;

	public function rules()
	{
		return array (
			array('area_name', 'required'),
			array('area_name', 'checkareaname'),
			array('managing_office', 'required'),
			array('officer_in_charge', 'required'),
			array('area_logo', 'file','types'=>'png, jpg, gif'),
		);
	}
	
	public function checkareaname()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'area_name';
		$criteria->condition = 'area_name=:area_name';
		$criteria->params = array(':area_name'=>$this->area_name);
		$count = Area::model()->count($criteria);
		
		if($count != 0)
		{
			$this->addError('area_name', 'Area name already exist in the database');
		}
	}
}