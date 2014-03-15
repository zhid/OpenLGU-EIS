<?php

Class AddMeasureThirdForm extends CFormModel
{
	//measure's row attributes
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
		if(isset(Yii::app()->session['number_of_rows']))
		{
			for($i=0; $i<Yii::app()->session['number_of_rows']; $i++)
			{
				$name_index = 'row'.$i.'_name';
				if(isset(Yii::app()->session[$name_index]))
				{
					if($this->column_name == Yii::app()->session[$name_index])
					{	
						$this->addError('column_name', 'A row name and a column Name must not be the same.');
					}
				}
			}
		}
	}
}