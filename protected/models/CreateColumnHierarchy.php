<?php

Class CreateColumnHierarchy extends CFormModel
{
	public $category_id;
	public $parent_id;

	public function rules()
	{
		return array (
			array('category_id, parent_id', 'required'),
		);
	}
	
	public function checkhierarchy($hierarchy_id)
	{
		if($this->parent_id != $this->category_id)
		{
			for($i=0; $i<count($hierarchy_id); $i++)
			{
				if(($hierarchy_id[$i]['id'] == $this->parent_id) && ($hierarchy_id[$i]['parent'] == $this->category_id))
				{
					$this->addError('parent_id', 'INVALID HIERARCHY!');
					return false;
					break;
				}
			}
		}
		return true;
	}
}