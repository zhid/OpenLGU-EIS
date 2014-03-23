<?php

Class AddMeasureSecondForm extends CFormModel
{
	//measure's row attributes
	public $row_name;
	public $row_data_type;

	public function rules()
	{
		return array (
			array('row_name, row_data_type', 'required'),
			array('row_name', 'checkrownamecharacters'),
		);
	}
	
	public function checkrownamecharacters()
	{
		if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $this->row_name))
		{
			$this->addError('row_name', 'Row name should not contain special characters');
		}
	}
	
	public function checkrowname()
	{
		if(isset(Yii::app()->session['number_of_columns']))
		{
			for($i=0; $i<Yii::app()->session['number_of_columns']; $i++)
			{
				$name_index = 'column'.$i.'_name';
				if(isset(Yii::app()->session[$name_index]))
				{
					if($this->row_name == Yii::app()->session[$name_index])
					{	
						$this->addError('row_name', 'A row name and a column Name must not be the same.');
					}
				}
			}
		}
	}
}