<?php

Class MainController extends CController
{
	public $defaultAction = 'servicearea';
	public $image_map = "";
	
	public function filters()
	{
		return array (
			'accessControl',
		);
	}
	
	public function accessRules()
	{
		return array (
			array('deny',
				'actions'=>array('querydata', 'renderNoCollapseAreaView', 'renderAreaView', 'renderNoCollapseBubbleView', 'renderBubbleView', 'renderNoCollapsePieView', 'renderPieView', 'renderNoCollapseLineView', 'renderLineView', 'renderNoCollapseBarView', 'renderBarView', 'renderTableView'),
				'users'=>array('?'),
			),
			array ('deny',
				'actions'=>array('index', 'panel', 'logout', 'main', 'dashboard', 'getdimension'),
				'users'=>array('?'),
			),
			array ('allow',
				'actions'=>array('index', 'panel', 'logout', 'getdimensions', 'dashboard', 'main'),
				'users'=>array('@'),
			),
		);
	}
	
	public function actionServicearea()
	{
		$this->render('servicearea');
	}

	public function actionPanel()
	{
		if(isset($_GET['servicearea']))
		{
			if($_GET['servicearea'] >= 1 && $_GET['servicearea'] <= 4)
			{
				$criteria = new CDbCriteria();
				$criteria->select = 'area_id, area_name, color_rating, area_logo';
				$criteria->condition = 'service_area=:service_area AND visible=:visible';
				$criteria->params = array(':service_area'=>$_GET['servicearea'], ':visible'=>true);
				$areas = Area::model()->findAll($criteria);
				$count = Area::model()->count($criteria);
				
				if($count != 0)
				{
					$this->render('main', array('areas'=>$areas, 'servicearea'=>$_GET['servicearea']));
				}
				else
				{
					Yii::app()->user->setFlash('main-flash', 'No Areas Found!');
					$url = $this->createUrl('/main/servicearea');
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
	
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->getHomeUrl());
	}
	
	public function actionDashboard()
	{
		if(isset($_GET['areaid']) && isset($_GET['servicearea']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'area_id=:area_id AND service_area=:service_area';
			$criteria->params = array(':area_id'=>$_GET['areaid'], ':service_area'=>$_GET['servicearea']);
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
					$this->render('dashboard', array('area'=>$area, 'measures'=>$measures, 'servicearea'=>$_GET['servicearea']));
				}
				else
				{	
					Yii::app()->user->setFlash('main-flash', ($area->area_name)." has no measure!");
					$url = $this->createUrl('/main/panel?servicearea='.$_GET['servicearea']);
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
			$rowParentId = explode(";", $_POST['rowParentId']);
			$rowDistanceLevel = $_POST['rowDistanceLevel'];
			$columnParentId = explode(";", $_POST['columnParentId']);
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
				$criteria->params = array(':distance_level'=>(int)$rowDistanceLevel);
			}
			else
			{
				$criteria->condition = 'distance_level=:distance_level AND parent_id=:parent_id';
				$criteria->params = array(':distance_level'=>(int)$rowDistanceLevel, ':parent_id'=>$rowParentId[$rowDistanceLevel]);
			}
			$row_hierarchies = RowHierarchy::model()->findAll($criteria);
			
			if($row_hierarchies == NULL)
			{
				echo "no-row-drilldown";
				return;
			}
			
			//find column dimension with distance level of 0
			$criteria = new CDbCriteria();
			$criteria->select = 'category_id';
			if($columnDistanceLevel == 0)
			{
				$criteria->condition = 'distance_level=:distance_level';
				$criteria->params = array(':distance_level'=>(int)$columnDistanceLevel);
			}
			else
			{
				$criteria->condition = 'distance_level=:distance_level AND parent_id=:parent_id';
				$criteria->params = array(':distance_level'=>(int)$columnDistanceLevel, ':parent_id'=>$columnParentId[$columnDistanceLevel]);
			}
			$column_hierarchies = ColumnHierarchy::model()->findAll($criteria);
			
			if($column_hierarchies == NULL)
			{
				echo "no-column-drilldown";
				return;
			}
			
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
			
			$rowParentId = explode(';', $_POST['rowParentId']);
			$rowParentValue = explode(';', $_POST['rowParentValue']);
			$row_hierarchy_condition = "";
			
			if($_POST['rowDistanceLevel'] != 0)
			{
				for($i=1; $i<count($rowParentId); $i++)
				{	
					$dimension = RowDimension::model()->find(array('select'=>'row_name', 'condition'=>'row_id=:row_id', 'params'=>array(':row_id'=>(int)$rowParentId[$i])));
					$dimension_name = preg_replace('/\s+/', '_', strtolower($dimension->row_name));
					
					$row_hierarchy_condition = $row_hierarchy_condition.''.$dimension_name.' = '.$rowParentValue[$i];
					if($i != count($rowParentId)-1)
					{
						$row_hierarchy_condition = $row_hierarchy_condition.' AND ';
					}
				}
			}
			
			//row filter
			for($i=0; $i<count($row_dim); $i++)
			{
				$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
				$attribute = preg_replace('/\s+/', '_', strtolower($row_dim[$i]));
				$rows = Yii::app()->db->createCommand()
					->selectDistinct($attribute)
					->from($table)
					->where($row_hierarchy_condition)
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
				
				$collapse = $_POST['chartCollapse'];
				
				$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
				$row_name = $_POST['rowName']; $row_name = explode(",", $row_name);
				$column_name = $_POST['columnName']; $column_name = explode(",", $column_name);
			
				$columns = $_POST['columnName'];
				$columns = preg_replace('/\s+/', '_', strtolower($columns));
				$columns = explode(",", $columns);
				
				$rows = $_POST['rowName'];
				$rows = preg_replace('/\s+/', '_', strtolower($rows));
				$rows = explode(",", $rows);
				
				$rowParentId = explode(';', $_POST['rowParentId']);
				$rowParentValue = explode(';', $_POST['rowParentValue']);
				$columnParentId = explode(';', $_POST['columnParentId']);
				$columnParentValue = explode(';', $_POST['columnParentValue']);
				$row_hierarchy_condition = ""; 
				$column_hierarchy_condition = "";
				$drilldown_hierarchy_condition = "";
				
				if($_POST['rowDistanceLevel'] != 0)
				{
					for($i=1; $i<count($rowParentId); $i++)
					{	
						$dimension = RowDimension::model()->find(array('select'=>'row_name', 'condition'=>'row_id=:row_id', 'params'=>array(':row_id'=>(int)$rowParentId[$i])));
						$dimension_name = preg_replace('/\s+/', '_', strtolower($dimension->row_name));
						
						$row_hierarchy_condition = $row_hierarchy_condition.''.$dimension_name.' = '.$rowParentValue[$i];
						if($i != count($rowParentId)-1)
						{
							$row_hierarchy_condition = $row_hierarchy_condition.' AND ';
						}
					}
				}
				if($_POST['columnDistanceLevel'] != 0)
				{
					for($i=1; $i<count($columnParentId); $i++)
					{
						$dimension = ColumnDimension::model()->find(array('select'=>'column_name', 'condition'=>'column_id=:column_id', 'params'=>array(':column_id'=>(int)$columnParentId[$i])));
						$dimension_name = preg_replace('/\s+/', '_', strtolower($dimension->column_name));
					
						$column_hierarchy_condition = $column_hierarchy_condition.''.$dimension_name.' = '.$columnParentValue[$i];
						if($i != count($columnParentId)-1)
						{
							$column_hierarchy_condition = $column_hierarchy_condition.' AND ';
						}
					}
				}
				if($row_hierarchy_condition != "" && $column_hierarchy_condition == "")
				{
					$drilldown_hierarchy_condition = $row_hierarchy_condition.' AND ';
				}
				else if($row_hierarchy_condition == "" && $column_hierarchy_condition != "")
				{
					//$drilldown_hierarchy_condition = $column_hierarchy_condition.' AND ';
					$drilldown_hierarchy_condition = "";
				}
				else if($row_hierarchy_condition != "" && $column_hierarchy_condition != "")
				{
					$drilldown_hierarchy_condition = $row_hierarchy_condition.' AND ';
				}
				else
				{
					$drilldown_hierarchy_condition = "";
				}
				
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
					$select_command = $select_command.' FROM '.$table.' WHERE '.$drilldown_hierarchy_condition.$where_command.$group_by_command.$order_by_command;
				}
				else
				{
					$select_command = $select_command.' FROM '.$table.' WHERE '.$drilldown_hierarchy_condition.$where_command.$group_by_command;
				}
				$table_rows = Yii::app()->db->createCommand($select_command)->queryAll();
				//echo $select_command;
				
				if(count($table_rows) != 0)
				{
					switch($_POST['chartCollapse'])
					{
						case 'Collapse':
							switch($_POST['viewMode'])
							{
								case 'table':
									$this->renderTableView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'bar':
									$this->renderBarView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'line':
									$this->renderLineView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'pie':
									$this->renderPieView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'bubble':
									$this->renderBubbleView($table, $table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'area':
									$this->renderAreaView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
							}
							break;
						case 'No Collapse':
							switch($_POST['viewMode'])
							{
								case 'table':
									$this->renderTableView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'bar':
									$this->renderNoCollapseBarView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'line':
									$this->renderNoCollapseLineView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'pie':
									$this->renderNoCollapsePieView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'bubble':
									$this->renderNoCollapseBubbleView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
								case 'area':
									$this->renderNoCollapseAreaView($table_rows, $columns, $column_name, $rows, $row_name);
									break;
							}
							break;
					}
				}
				else
				{
					echo '<div id="comment-on-data">No Data Found!</div>';
				}
			}
		}
		catch(Exception $exception) {
			echo $exception;
		}
	}
	
	function renderNoCollapseAreaView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		$chart_data = array();
		$chart_data_count = 0;
		$chart_label = array();
		
		for($i=0; $i<count($column_name); $i++)
		{
			$chart_label[$i] = $column_name[$i];
		}
		
		foreach($table_rows as $table_row)
		{
			$each_data_count = 0;
			$label = "";
			for($j=0; $j<count($rows); $j++)
			{
				$label = $label.''.$table_row[$rows[$j]];
				if($j != count($rows)-1)
				{
					$label = $label.'/';
				}
			}
			
			$chart_data[$chart_data_count][$each_data_count++] = $label;
			
			for($j=0; $j<count($columns); $j++)
			{
				$chart_data[$chart_data_count][$each_data_count++] = $table_row[$columns[$j]]; 
			}	
			$chart_data_count++;
		}
		
		$plot = new PHPlot(740, 400);
		
		$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
		$plot->SetDefaultTTFont('ARIAL.TTF');
		$plot->SetFont('title', 'ARIALBD.TTF', 10);
		$plot->SetFont('y_label', 'ARIAL.TTF', 8);
		$plot->SetFont('x_label', 'ARIAL.TTF', 8);
		$plot->SetLegend($chart_label);
		$plot->SetPrintImage(false);
		$plot->SetImageBorderType('none');
		$plot->SetPlotType('stackedarea');
		$plot->SetDataType('text-data');
		$plot->SetDataValues($chart_data);
		$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
		$plot->SetXTickLabelPos('none');
		$plot->SetXTickPos('none');
		$plot->DrawGraph();
	
		echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
	}
	
	function renderAreaView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
	
		for($i=0; $i<count($columns); $i++)
		{
			$chart_data = array();
			$chart_data_counter = 0;
			foreach($table_rows as $table_row) 
			{
				$label = "";
				for($j=0; $j<count($rows); $j++)
				{
					$label = $label.''.$table_row[$rows[$j]];
					if($j != count($rows)-1)
					{
						$label = $label.'/';
					}
				}
				$chart_data[$chart_data_counter] = array($label, $table_row[$columns[$i]]);
				
				$chart_data_counter++;
			}
			
			$chart = new Chart();
		
			if(count($columns) == 1)
			{
				$plot = new PHPlot(740, 400);
			}
			else if(count($columns) >= 2)
			{
				$plot = new PHPlot(370, 400);
			}
			
			$plot->SetPrintImage(false);
			$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
			$plot->SetDefaultTTFont('ARIAL.TTF');
			$plot->SetFont('title', 'ARIALBD.TTF', 10);
			$plot->SetFont('x_label', 'ARIAL.TTF', 8);
			$plot->SetFont('y_label', 'ARIAL.TTF', 8);
			$plot->SetImageBorderType('none');
			$plot->SetPlotType('stackedarea');
			$plot->SetDataType('text-data');
			$plot->SetDataValues($chart_data);
			$plot->SetTitle($column_name[$i]);
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
			$plot->SetXTickLabelPos('none');
			$plot->SetXTickPos('none');
			$plot->DrawGraph();
		
			echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
		}
	}
	
	function renderNoCollapseBubbleView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
	
		$chart_data = array();
		$chart_data_counter = 0;
		$entry_counter = 1;
		$chart_label_y = array();
		$chart_label_y[0] = "";
		
		foreach($table_rows as $table_row) 
		{
			$label = "";
			for($j=0; $j<count($rows); $j++)
			{
				$label = $label.''.$table_row[$rows[$j]];
				if($j != count($rows)-1)
				{
					$label = $label.'/';
				}
			}
			$chart_data[$chart_data_counter][0] = $label;
			$chart_data[$chart_data_counter][1] = $entry_counter++;
			$y_counter = 1;
			
			$chart_data_index = 2;
			$label_index = 1;
			for($i=0; $i<count($columns); $i++)
			{
				$chart_data[$chart_data_counter][$chart_data_index++] = $y_counter++;
				$chart_data[$chart_data_counter][$chart_data_index++] = $table_row[$columns[$i]];
				
				$chart_label_y[$label_index++] = $column_name[$i];
			}
			$chart_label_y[$label_index] = "";
			$chart_data_counter++;
		}
		$max_bubble_size = 0;
		$bubble_denom = 9;
		$max_bubble_size = 400 / $bubble_denom;
		
		$chart = new Chart();
		$plot = new PHPlot(740, 400);
		$plot->SetPrintImage(false);
		$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
		$plot->SetDefaultTTFont('ARIAL.TTF');
		$plot->SetFont('title', 'ARIALBD.TTF', 10);
		$plot->SetFont('x_label', 'ARIAL.TTF', 8);
		$plot->SetFont('y_label', 'ARIAL.TTF', 8);
		$plot->SetDataType('data-data-xyz');
		$plot->SetDataValues($chart_data);
		$plot->SetPlotType('bubbles');
		$plot->SetDataColors('yellow');
		$plot->SetDrawPlotAreaBackground(True);
		$plot->SetPlotBgColor('SkyBlue');
		$plot->SetLightGridColor('red');
		$plot->SetImageBorderType('plain');
		$plot->bubbles_max_size = $max_bubble_size;
		$plot->bubbles_min_size = $max_bubble_size / 5;
		$plot->SetPlotBorderType('full');
		if(count($columns) > 1)
		{
			$plot->SetXTickIncrement(1);
			$plot->SetYTickIncrement(1);
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
		}
		else
		{
			$plot->SetXTickIncrement(1);
			$plot->SetYTickIncrement(1);
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, 2);
		}
		$plot->SetYLabelType('custom', array($chart, 'get_label'), $chart_label_y);
		$plot->SetXTickPos('both');
		$plot->SetYTickPos('both');
		$plot->SetXDataLabelPos('both');
		$plot->SetYTickLabelPos('plotleft');
		$plot->SetDrawXGrid(True);
		$plot->DrawGraph();
		
		echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
	}
	
	function renderBubbleView($table, $table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
	
		for($i=0; $i<count($columns); $i++)
		{
			$chart_data = array();
			$chart_data_counter = 0;
			foreach($table_rows as $table_row) 
			{
				$label = "";
				for($j=0; $j<count($rows); $j++)
				{
					$label = $label.''.$table_row[$rows[$j]];
					if($j != count($rows)-1)
					{
						$label = $label.'/';
					}
				}
				$chart_data[$chart_data_counter] = array($label, $chart_data_counter+1, 1, $table_row[$columns[$i]]);
				//echo implode(' : ', $chart_data[$chart_data_counter]).'<br/>';
				$chart_data_counter++;
			}
			
			$chart = new Chart();
			$max_bubble_size = 0;
			$bubble_denom = 9;
			
			if(count($columns) == 1)
			{
				$plot = new PHPlot(740, 400);
				$max_bubble_size = 400 / $bubble_denom;
			}
			else if(count($columns) >= 2)
			{
				$plot = new PHPlot(370, 400);
				$max_bubble_size = 400 / $bubble_denom;
			}
			
			
			$chart = new Chart();
			$plot->SetPrintImage(false);
			$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
			$plot->SetDefaultTTFont('ARIAL.TTF');
			$plot->SetFont('title', 'ARIALBD.TTF', 10);
			$plot->SetFont('x_label', 'ARIAL.TTF', 8);
			$plot->SetTitle($column_name[$i]);
			$plot->SetDataType('data-data-xyz');
			$plot->SetDataValues($chart_data);
			$plot->SetPlotType('bubbles');
			$plot->bubbles_max_size = $max_bubble_size;
			$plot->bubbles_min_size = $max_bubble_size / 5;
			$plot->SetDataColors('yellow');
			$plot->SetDrawPlotAreaBackground(True);
			$plot->SetPlotBgColor('SkyBlue');
			$plot->SetLightGridColor('red');
			$plot->SetImageBorderType('none');
			$plot->SetPlotBorderType('full');
			$plot->SetXTickIncrement(1);
			$plot->SetYTickIncrement(1);
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, 2);
			
			//establish the handler for the Y label text:
			$plot->SetYLabelType('custom', array($chart, 'get_label'), array("", "", ""));
			$plot->SetXTickPos('both');
			$plot->SetYTickPos('none');
			$plot->SetXDataLabelPos('both');
			$plot->SetYTickLabelPos('plotleft');
			$plot->SetDrawXGrid(True);
			$plot->SetDrawYGrid(False);
			$plot->DrawGraph();
			
			echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
		}
	}
	
	function renderNoCollapsePieView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		$chart_data = array();
		$chart_data_count = 0;
		$chart_label = array();
		
		for($i=0; $i<count($column_name); $i++)
		{
			$chart_label[$i] = $column_name[$i];
		}
		
		foreach($table_rows as $table_row)
		{
			$each_data_count = 0;
			$label = "";
			for($j=0; $j<count($rows); $j++)
			{
				$label = $label.''.$table_row[$rows[$j]];
				if($j != count($rows)-1)
				{
					$label = $label.'/';
				}
			}
			
			$chart_data[$chart_data_count][$each_data_count++] = '';
			
			for($j=0; $j<count($columns); $j++)
			{
				$chart_data[$chart_data_count][$each_data_count++] = $table_row[$columns[$j]]; 
			}	
			$chart_data_count++;
		}
		
		$plot = new PHPlot(740, 400);
		
		$plot->SetPrintImage(False);
		$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
		$plot->SetDefaultTTFont('ARIAL.TTF');
		$plot->SetFont('title', 'ARIALBD.TTF', 10);
		$plot->SetFont('x_label', 'ARIAL.TTF', 8);
		$plot->SetImageBorderType('none');
		$plot->SetDataType('text-data');
		$plot->SetLegend($chart_label);
		$plot->SetDataValues($chart_data);
		$plot->SetPlotType('pie');
		$plot->SetShading(0);
		$plot->SetDrawPieBorders(True);
		$plot->SetPieBorderColor('white');
		//list($width, $height) = $plot->GetLegendSize();
		$plot->SetPieAutoSize(true);
		//$plot->SetMarginsPixels($width, NULL, NULL, NULL);
		//$plot->SetLegendPosition(0, 0, 'image', 0, 0, 5, 20);
		
		$plot->DrawGraph();
		
		echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
	}
	
	function renderPieView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		for($i=0; $i<count($columns); $i++)
		{
			$chart_data = array();
			$chart_data_count = 0;
			$legend_count=0;
			$legend = array();
			foreach($table_rows as $table_row)
			{
				$label = "";
				for($j=0; $j<count($rows); $j++)
				{
					$label = $label.''.$table_row[$rows[$j]];
					if($j != count($rows)-1)
					{
						$label = $label.'/';
					}
				}
				$chart_data[$chart_data_count++] = array($label, $table_row[$columns[$i]]);
				$legend[$legend_count++] = $label;
			}
			
			//create and configure the PHPlot object.
			if(count($columns) == 1)
			{
				$plot = new PHPlot(740, 400);
			}
			else if(count($columns) >= 2)
			{
				$plot = new PHPlot(370, 400);
			}
			
			$plot->SetPrintImage(False);
			$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
			$plot->SetDefaultTTFont('ARIAL.TTF');
			$plot->SetFont('title', 'ARIALBD.TTF', 10);
			$plot->SetFont('x_label', 'ARIAL.TTF', 8);
			$plot->SetTitle($column_name[$i]);
			$plot->SetImageBorderType('none');
			$plot->SetDataType('text-data-single');
			$plot->SetLegend($legend);
			$plot->SetDataValues($chart_data);
			$plot->SetPlotType('pie');
			$plot->SetShading(0);
			$plot->SetDrawPieBorders(True);
			$plot->SetPieBorderColor('white');
			list($width, $height) = $plot->GetLegendSize();
			$plot->SetPieAutoSize(true);
			$plot->SetMarginsPixels($width, NULL, NULL, NULL);
			$plot->SetLegendPosition(0, 0, 'image', 0, 0, 5, 20);
			
			$plot->DrawGraph();
			
			echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
		}
	}
	
	function renderNoCollapseLineView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		$chart_data = array();
		$chart_data_count = 0;
		$chart_label = array();
		
		for($i=0; $i<count($column_name); $i++)
		{
			$chart_label[$i] = $column_name[$i];
		}
		
		foreach($table_rows as $table_row)
		{
			$each_data_count = 0;
			$label = "";
			for($j=0; $j<count($rows); $j++)
			{
				$label = $label.''.$table_row[$rows[$j]];
				if($j != count($rows)-1)
				{
					$label = $label.'/';
				}
			}
			
			$chart_data[$chart_data_count][$each_data_count++] = $label;
			
			for($j=0; $j<count($columns); $j++)
			{
				$chart_data[$chart_data_count][$each_data_count++] = $table_row[$columns[$j]]; 
			}	
			$chart_data_count++;
		}
		
		$plot = new PHPlot(740, 400);
			
		
		//set up the rest of the plot:
		$plot->SetPrintImage(False);
		$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
		$plot->SetLegend($chart_label);
		$plot->SetDefaultTTFont('ARIAL.TTF');
		$plot->SetFont('title', 'ARIALBD.TTF', 10);
		$plot->SetFont('y_label', 'ARIAL.TTF', 8);
		$plot->SetFont('x_label', 'ARIAL.TTF', 8);
		$plot->SetImageBorderType('none');
		$plot->SetPlotType('linepoints');
		$plot->SetDataType('text-data');
		$plot->SetDataValues($chart_data);
		$plot->SetYDataLabelPos('plotin');
		$plot->SetXTickPos('none');

		//set the data_points callback which will generate the image map.
		$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
		
		//produce the graph; this also creates the image map via callback:
		$plot->DrawGraph();

		echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
	}
	
	function renderLineView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		for($i=0; $i<count($columns); $i++)
		{
			$chart_data = array();
			$chart_data_count = 0;
			$line_number = 1.0;
			foreach($table_rows as $table_row)
			{
				$label = "";
				for($j=0; $j<count($rows); $j++)
				{
					$label = $label.''.$table_row[$rows[$j]];
					if($j != count($rows)-1)
					{
						$label = $label.'/';
					}
				}
				$chart_data[$chart_data_count++] = array($label, $line_number, $table_row[$columns[$i]]);
				$line_number = $line_number + 1.0;
			}
			$chart_data[$chart_data_count] = array('', $line_number, );
		
			//create and configure the PHPlot object.
			if(count($columns) == 1)
			{
				$plot = new PHPlot(740, 400);
			}
			else if(count($columns) >= 2)
			{
				$plot = new PHPlot(370, 400);
			}
			
			//set up the rest of the plot:
			$plot->SetPrintImage(False);
			$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
			$plot->SetDefaultTTFont('ARIAL.TTF');
			$plot->SetFont('title', 'ARIALBD.TTF', 10);
			$plot->SetFont('y_label', 'ARIAL.TTF', 8);
			$plot->SetFont('x_label', 'ARIAL.TTF', 8);
			$plot->SetImageBorderType('none');
			$plot->SetPlotType('linepoints');
			$plot->SetDataType('data-data');
			$plot->SetDataValues($chart_data);
			$plot->SetTitle($column_name[$i]);
			$plot->SetYDataLabelPos('plotin');
			$plot->SetXTickIncrement(1.0);
			$plot->SetDataColors(array('SlateBlue'));
			$plot->SetPointShapes(array('dot'));

			//set the data_points callback which will generate the image map.
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
			
			//produce the graph; this also creates the image map via callback:
			$plot->DrawGraph();

			echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image">';
		}
	}
	
	function renderNoCollapseBarView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		$chart_data = array();
		$chart_data_count = 0;
		$chart_label = array();
		
		for($i=0; $i<count($column_name); $i++)
		{
			$chart_label[$i] = $column_name[$i];
		}
		
		foreach($table_rows as $table_row)
		{
			$each_data_count = 0;
			$label = "";
			for($j=0; $j<count($rows); $j++)
			{
				$label = $label.''.$table_row[$rows[$j]];
				if($j != count($rows)-1)
				{
					$label = $label.'/';
				}
			}
			
			$chart_data[$chart_data_count][$each_data_count++] = $label;
			
			for($j=0; $j<count($columns); $j++)
			{
				$chart_data[$chart_data_count][$each_data_count++] = $table_row[$columns[$j]]; 
			}	
			$chart_data_count++;
		}
		
		$chart = new Chart();
		
		$plot = new PHPlot(740, 400);
		
		//Disable error images, since this script produces HTML:
		$plot->SetFailureImage(False);
		
		//disable automatic output of the image by DrawGraph():
		$plot->SetPrintImage(False);
		
		//set up the rest of the plot:
		$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
		$plot->SetDefaultTTFont('ARIAL.TTF');
		$plot->SetFont('title', 'ARIALBD.TTF', 10);
		$plot->SetFont('x_label', 'ARIAL.TTF', 8);
		$plot->SetFont('y_label', 'ARIAL.TTF', 8);
		$plot->SetLegend($chart_label);
		$plot->SetShading(0);
		$plot->SetImageBorderType('none');
		$plot->SetDataValues($chart_data);
		$plot->SetDataType('text-data');
		$plot->SetPlotType('bars');
		$plot->SetXTickPos('none');
		$plot->SetYDataLabelPos('plotin');
		
		//set the data_points callback which will generate the image map.
		$plot->SetCallback('data_points', array($chart, 'drawBarChart'));
		$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
		
		//produce the graph; this also creates the image map via callback:
		$plot->DrawGraph();

		echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image" usemap="#map">';
	}
	
	function renderBarView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		require_once 'chart/phplot.php';
		
		$chart_data = array();
		
		for($i=0; $i<count($columns); $i++)
		{
			$chart_data_count = 0;
			foreach($table_rows as $table_row)
			{
				$label = "";
				for($j=0; $j<count($rows); $j++)
				{
					$label = $label.''.$table_row[$rows[$j]];
					if($j != count($rows)-1)
					{
						$label = $label.'/';
					}
				}
				$chart_data[$chart_data_count++] = array($label, $table_row[$columns[$i]]);
			}
			
			$legend = "";
			for($legend_count=0; $legend_count<count($row_name); $legend_count++)
			{
				$legend = $legend.$row_name[$legend_count];
				if($legend_count != count($row_name)-1)
				{
					$legend = $legend.'/';
				}
			}
			
			$chart = new Chart();
		
			//create and configure the PHPlot object.
			
			if(count($columns) == 1)
			{
				$plot = new PHPlot(740, 400);
			}
			else if(count($columns) >= 2)
			{
				$plot = new PHPlot(370, 400);
			}
			
			//Disable error images, since this script produces HTML:
			$plot->SetFailureImage(False);
			
			//disable automatic output of the image by DrawGraph():
			$plot->SetPrintImage(False);
			
			//set up the rest of the plot:
			$plot->SetTTFPath(Yii::getPathOfAlias('webroot.fonts'));
			$plot->SetDefaultTTFont('ARIAL.TTF');
			$plot->SetFont('title', 'ARIALBD.TTF', 10);
			$plot->SetFont('x_label', 'ARIAL.TTF', 8);
			$plot->SetFont('y_label', 'ARIAL.TTF', 8);
			$plot->SetTitle($column_name[$i]);
			$plot->SetShading(0);
			$plot->SetImageBorderType('none');
			$plot->SetDataValues($chart_data);
			$plot->SetDataType('text-data');
			$plot->SetPlotType('bars');
			$plot->SetXTickPos('none');
			$plot->SetYDataLabelPos('plotin');
			
			//set the data_points callback which will generate the image map.
			$plot->SetCallback('data_points', array($chart, 'drawBarChart'));
			$plot->SetPlotAreaWorld(NULL, NULL, NULL, NULL);
			
			//produce the graph; this also creates the image map via callback:
			$plot->DrawGraph();

			echo '<img src="'.$plot->EncodeImage().'" alt="Plot Image" usemap="#map">';
		}
	}
	
	function renderTableView($table_rows, $columns, $column_name, $rows, $row_name)
	{
		echo '<table>';
		echo '<tr>';
		for($i=0; $i<count($rows); $i++)
		{
			echo '<th>'.$row_name[$i].'</th>';
		}
		for($i=0; $i<count($columns); $i++)
		{
			$column_dimension = ColumnDimension::model()->find(array('select'=>'column_id, column_data_type', 'condition'=>'column_name=:column_name', 'params'=>array(':column_name'=>$column_name[$i])));
			$column_hierarchy = ColumnHierarchy::model()->find(array('select'=>'category_id, top_flag, bottom_flag, distance_level', 'condition'=>'category_id=:category_id', 'params'=>array(':category_id'=>$column_dimension->column_id)));
		
			echo '<th class="drill-down" cell="column" type="'.($column_dimension->column_data_type).'" distance="'.($column_hierarchy->distance_level).'" isbottom="'.($column_hierarchy->bottom_flag).'" isTop="'.($column_hierarchy->top_flag).'" columnId="'.($column_hierarchy->category_id).'" columnname="'.$column_name[$i].'" oncontextmenu="return showContext(event, this)">'.$column_name[$i].'</th>';
		}
		echo '</tr>';
		foreach($table_rows as $table_row)
		{
			echo '<tr>';
			for($i=0; $i<count($rows); $i++)
			{
				$row_dimension = RowDimension::model()->find(array('select'=>'row_id, row_data_type', 'condition'=>'row_name=:row_name', 'params'=>array(':row_name'=>$row_name[$i])));
				$row_hierarchy = RowHierarchy::model()->find(array('select'=>'category_id, top_flag, bottom_flag, distance_level', 'condition'=>'category_id=:category_id', 'params'=>array(':category_id'=>$row_dimension->row_id)));
				
				echo '<td class="drill-down" cell="row" type="'.($row_dimension->row_data_type).'" distance="'.($row_hierarchy->distance_level).'" isbottom="'.($row_hierarchy->bottom_flag).'" isTop="'.($row_hierarchy->top_flag).'" rowId="'.($row_hierarchy->category_id).'" rowname="'.$rows[$i].'" rowdata="'.$table_row[$rows[$i]].'" oncontextmenu="return showContext(event, this)">'.$table_row[$rows[$i]].'</td>';
			}
			for($i=0; $i<count($columns); $i++)
			{
				echo '<td>'.$table_row[$columns[$i]].'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
}