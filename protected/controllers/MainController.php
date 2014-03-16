<?php

Class MainController extends CController
{
	public $defaultAction = 'panel';
	
	public function filters()
	{
		return array (
			'accessControl',
		);
	}
	
	public function accessRules()
	{
		return array (
			array ('deny',
				'actions'=>array('panel', 'logout'),
				'users'=>array('?'),
			),
			array ('allow',
				'actions'=>array('index', 'panel', 'logout', 'getdimensions', 'dashboard'),
				'users'=>array('@'),
			),
		);
	}

	public function actionPanel()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'area_id, area_name, area_logo, color_rating';
		$criteria->condition = 'visible=:visible';
		$criteria->params = array(':visible'=>true);
		
		$areas = Area::model()->findAll($criteria);
		$count = Area::model()->count($criteria);
		$rem = $count%5;
		
		if($rem < 5){$rem = 5;}
		
		$this->render('main', array('areas'=>$areas, 'count'=>$count, 'size'=>round(($count/5)+$rem, 0, PHP_ROUND_HALF_DOWN)));
	}
	
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->getHomeUrl());
	}
	
	public function actionDashboard()
	{
		if(isset($_GET['areaid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			if($area != NULL)
			{
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'area_id=:area_id';
				$criteria->order = 'measure_id ASC';
				$criteria->params = array(':area_id'=>$_GET['areaid']);
				$measures = Measure::model()->findAll($criteria);
			
				$this->render('dashboard', array('area'=>$area, 'measures'=>$measures));
			}
			else
			{
			
			}
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	function actionGetdimensions()
	{
		if(isset($_POST['measureid']) && isset($_POST['isAjax']))
		{
			$rowParentId = $_POST['rowParentId'];
			$rowDistanceLevel = $_POST['rowDistanceLevel'];
			$columnParentId = $_POST['columnParentId'];
			$columnDistanceLevel = $_POST['columnDistanceLevel'];
		
			//find measure name
			$criteria = new CDbCriteria();
			$criteria->select = 'measure_name';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$measure = Measure::model()->find($criteria);
		
			//find row dimension with distance level of 0
			$criteria = new CDbCriteria();
			$criteria->select = 'category_id';
			if($rowDistanceLevel == 0)
			{
				$criteria->condition = 'distance_level=:distance_level';
				$criteria->params = array(':distance_level'=>$rowDistanceLevel);
			}
			else
			{
				$criteria->condition = 'distance_level=:distance_level AND parent_id=:parent_id';
				$criteria->params = array(':distance_level'=>$rowDistanceLevel, ':parent_id'=>$rowParentId);
			}
			$row_hierarchies = RowHierarchy::model()->findAll($criteria);
			
			$row_root = array();
			$i = 0;
			foreach($row_hierarchies as $row_hierarchy)
			{
				$row_root[$i++] = $row_hierarchy->category_id;
			}
		
			//get row dimensions
			$criteria = new CDbCriteria();
			$criteria->select = 'row_id, row_name';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$criteria->addInCondition('row_id', $row_root);
			$rows = RowDimension::model()->findAll($criteria);
			
			$radiobutton_row = array();
			$row_dim = array();
			$selected;
			$i = 0;
			foreach($rows as $row)
			{
				$radiobutton_row[$row->row_name] = $row->row_name;
				if($i == 0){$selected = $row->row_name;}
				
				$row_dim[$i] = $row->row_name;
				$i++;
			}
			
			echo '<div class="dashboard-side-name">Rows</div>';
			echo '<div class="dimensions-checkbox">';
			echo CHtml::beginForm('main', 'POST', array('name'=>'row_dimension_form'));
			echo CHtml::checkBoxList('rows', $selected, $radiobutton_row, array('onchange'=>'rowList(this)'));
			echo CHtml::endForm();
			echo '</div>';
			
			//row filter
			for($i=0; $i<count($row_dim); $i++)
			{
				$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
				$attribute = preg_replace('/\s+/', '_', strtolower($row_dim[$i]));
				$rows = Yii::app()->db->createCommand()
					->selectDistinct($attribute)
					->from($table)
					->queryAll();
				
				$row_value_list = array();
				$selected = array(); $j = 0;
				foreach($rows as $row)
				{
					$row_value_list[$row[$attribute]] = $row[$attribute];
					$selected[$j++] = $row[$attribute];
				}	
					
				echo '<div class="filter_container" id="'.$attribute.'_container" style="display:none;">';
				echo '<div class="filter_name">Filters</div>';
				echo CHtml::beginForm('main', 'POST', array('id'=>$attribute.'_form'));
				echo CHtml::checkBoxList($attribute.'_values', $selected, $row_value_list);
				echo CHtml::endForm();
				echo '<div class="filter_button"><button onclick="rowFilterButton('.$attribute.'_container)">Ok</button></div>';
				echo '</div>';
			}
			//end of row filter
			
			//find column dimension with distance level of 0
			$criteria = new CDbCriteria();
			$criteria->select = 'category_id';
			if($columnDistanceLevel == 0)
			{
				$criteria->condition = 'distance_level=:distance_level';
				$criteria->params = array(':distance_level'=>$columnDistanceLevel);
			}
			else
			{
				$criteria->condition = 'distance_level=:distance_level AND parent_id=:parent_id';
				$criteria->params = array(':distance_level'=>$columnDistanceLevel, ':parent_id'=>$columnParentId);
			}
			$column_hierarchies = ColumnHierarchy::model()->findAll($criteria);
			
			$column_root = array();
			$i = 0;
			foreach($column_hierarchies as $column_hierarchy)
			{
				$column_root[$i++] = $column_hierarchy->category_id;
			}
		
			//get column dimension
			$criteria = new CDbCriteria();
			$criteria->select = 'column_id, column_name';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$criteria->addInCondition('column_id', $column_root);
			$columns = ColumnDimension::model()->findAll($criteria);
			
			$radiobutton_column = array();
			$selected;
			$i = 0;
			
			foreach($columns as $column)
			{
				$radiobutton_column[$column->column_name] = $column->column_name;
				if($i == 0){$selected = $column->column_name; $i++;}
			}
			
			echo '<div class="dashboard-side-name">Columns</div>';
			echo '<div class="dimensions-checkbox">';
			echo CHtml::beginForm('main', 'POST', array('name'=>'column_dimension_form'));
			echo CHtml::checkBoxList('columns', $selected, $radiobutton_column, array('onchange'=>'columnList(this)'));
			echo CHtml::endForm();
			echo '</div>';
		}
		else
		{
			throw new CHttpException(404);
		}
	}
}