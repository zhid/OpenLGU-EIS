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
			
				if($measures != NULL)
				{
					$this->render('dashboard', array('area'=>$area, 'measures'=>$measures));
				}
				else
				{	
					Yii::app()->user->setFlash('main-flash', ($area->area_name)." has no measure!");
					$url = $this->createUrl('/main');
					$this->redirect($url);
				}
			}
			else
			{
				throw new CHttpException(404);
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
			$criteria->select = 'row_id, row_name, row_data_type';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$criteria->addInCondition('row_id', $row_root);
			$rows = RowDimension::model()->findAll($criteria);
			
			$radiobutton_row = array();
			$row_dim = array();
			$row_dim_type = array();
			$selected;
			$i = 0;
			foreach($rows as $row)
			{
				$radiobutton_row[$row->row_name] = $row->row_name;
				if($i == 0){$selected = $row->row_name;}
				
				$row_dim[$i] = $row->row_name;
				$row_dim_type[$i] = $row->row_data_type;
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

				if($row_dim_type[$i] == 'text')
				{
					foreach($rows as $row)
					{
						$row_value_list["'".$row[$attribute]."'"] = $row[$attribute];
						$selected[$j++] = "'".$row[$attribute]."'";
					}	
				}
				else
				{
					foreach($rows as $row)
					{
						$row_value_list[$row[$attribute]] = $row[$attribute];
						$selected[$j++] = $row[$attribute];
					}	
				}
				
				echo '<div class="filter_container" id="'.$attribute.'_container" style="display:none;">';
				echo '<div class="filter_name">'.$row_dim[$i].' Filter</div>';
				echo '<div id="filter-data">';
				echo CHtml::beginForm('main', 'POST', array('name'=>$attribute.'_form'));
				echo CHtml::checkBoxList($attribute.'_values', $selected, $row_value_list);
				echo CHtml::endForm();
				echo '</div>';
				echo '<div class="filter_button"><button onclick="rowFilterButton('.$attribute.'_container)">Filter</button></div>';
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
			$criteria->select = 'column_id, column_name, column_data_type';
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
				
				$attribute = preg_replace('/\s+/', '_', strtolower($column->column_name));
				echo '<div class="filter_container" id="'.$attribute.'_container" style="display:none;">';
				echo '<div class="filter_name">Sort '.$column->column_name.' By:</div>';
				echo '<div id="filter-data">';
				echo CHtml::beginForm('main', 'POST', array('name'=>$attribute.'_form'));
				echo CHtml::radioButtonList($attribute.'_values', 'NONE', array('NONE'=>'None', 'ASC'=>'Ascending', 'DESC'=>'Descending'));
				echo CHtml::endForm();
				echo '</div>';
				echo '<div class="filter_button"><button onclick="columnFilterButton('.$attribute.'_container)">Sort</button></div>';
				echo '</div>';
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
	
	function actionQuerydata()
	{
		try
		{
			if(isset($_POST['isAjax']))
			{
				$criteria = new CDbCriteria();
				$criteria->select = 'measure_name';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->params = array(':measure_id'=>$_POST['measureId']);
				$measure = Measure::model()->find($criteria);
				
				$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
				$row_name = $_POST['rowName']; $row_name = explode(",", $row_name);
				$column_name = $_POST['columnName']; $column_name = explode(",", $column_name);
			
				$columns = $_POST['columnName'];
				$columns = preg_replace('/\s+/', '_', strtolower($columns));
				$columns = explode(",", $columns);
				
				$rows = $_POST['rowName'];
				$rows = preg_replace('/\s+/', '_', strtolower($rows));
				$rows = explode(",", $rows);
				
				$select_command = 'SELECT ';
				$where_command = '';
				$group_by_command = ' GROUP BY ';
				$order_by_command = ' ORDER BY ';
				$no_filter = "'no filter'";
				for($i=0; $i<count($rows); $i++)
				{
					$select_command = $select_command.''.$rows[$i].', ';
					if($_POST[$rows[$i].'_values'] !== '')
					{
						$where_command = $where_command .''.$rows[$i].' IN ('.$_POST[$rows[$i].'_values'].')';
					}
					else
					{
						$where_command = $where_command .''.$rows[$i].' IN ('.$no_filter.')';
					}
					$group_by_command = $group_by_command.''.$rows[$i];
					if($i != count($rows)-1)
					{ 
						$where_command  = $where_command .' AND '; 
						$group_by_command = $group_by_command.', ';
					}
				}
				$order_count = 0;
				for($i=0; $i<count($columns); $i++)
				{
					$select_command = $select_command.'sum('.$columns[$i].') "'.$columns[$i].'"';
					if($_POST[$columns[$i].'_sort'] !== 'NONE')
					{
						if($order_count === 0)
						{
							$order_by_command = $order_by_command.''.$columns[$i].' '.$_POST[$columns[$i].'_sort'];
						}
						else
						{
							$order_by_command = $order_by_command.', '.$columns[$i].' '.$_POST[$columns[$i].'_sort'];
						}
						$order_count++;
					}
					if($i != count($columns)-1)
					{ 
						$select_command = $select_command.', ';
					}
				}
				if($order_by_command !== ' ORDER BY ')
				{
					$select_command = $select_command.' FROM '.$table.' WHERE '.$where_command.$group_by_command.$order_by_command;
				}
				else
				{
					$select_command = $select_command.' FROM '.$table.' WHERE '.$where_command.$group_by_command;
				}
				$table_rows = Yii::app()->db->createCommand($select_command)->queryAll();
				
				if(count($table_rows) != 0)
				{
				echo '<table>';
				echo '<tr>';
				for($i=0; $i<count($rows); $i++)
				{
					echo '<th>'.$row_name[$i].'</th>';
				}
				for($i=0; $i<count($columns); $i++)
				{
					echo '<th>'.$column_name[$i].'</th>';
				}
				echo '</tr>';
				foreach($table_rows as $table_row)
				{
					echo '<tr>';
					for($i=0; $i<count($rows); $i++)
					{
						echo '<td class="drill-down" rowname="'.$rows[$i].'" rowdata="'.$table_row[$rows[$i]].'" onclick="rowDrillDown(this)">'.$table_row[$rows[$i]].'</td>';
					}
					for($i=0; $i<count($columns); $i++)
					{
						echo '<td class="drill-down" columnname="'.$columns[$i].'" columndata="'.$table_row[$columns[$i]].'" onclick="columnDrillDown(this)">'.$table_row[$columns[$i]].'</td>';
					}
					echo '</tr>';
				}
				echo '</table>';
				}
				else
				{
					echo '<div id="comment-on-data">No Data Found!</div>';
				}
				//echo $select_command;
			}
		}
		catch(Exception $exception) {
			echo $exception;
		}
	}
}