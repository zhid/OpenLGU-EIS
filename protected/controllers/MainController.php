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
				'actions'=>array('index', 'panel', 'logout'),
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
			//get row dimensions
			$criteria = new CDbCriteria();
			$criteria->select = 'row_id, row_name';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$rows = RowDimension::model()->findAll($criteria);
			
			$checkbox_row = array();
			$row_names = array();
			$i = 0;
			foreach($rows as $row)
			{
				$checkbox_row[$row->row_name] = $row->row_name;
				$row_names[$i++] = $row->row_name;
			}
			
			echo '<div class="dashboard-side-name">Rows</div>';
			echo '<div class="dimensions-checkbox">';
			echo CHtml::beginForm('main', 'POST', array('name'=>'row_dimension_form'));
			echo CHtml::checkBoxList('rows', $row_names, $checkbox_row);
			echo CHtml::endForm();
			echo '</div>';
		
			//get column dimension
			$criteria = new CDbCriteria();
			$criteria->select = 'column_id, column_name';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid']);
			$columns = ColumnDimension::model()->findAll($criteria);
			
			$checkbox_column = array();
			$column_names = array();
			$i = 0;
			foreach($columns as $column)
			{
				$checkbox_column[$column->column_name] = $column->column_name;
				$column_names[$i++] = $column->column_name;
			}
			
			echo '<div class="dashboard-side-name">Columns</div>';
			echo '<div class="dimensions-checkbox">';
			echo CHtml::beginForm('main', 'POST', array('name'=>'column_dimension_form'));
			echo CHtml::checkBoxList('columns', $column_names, $checkbox_column, array('onchange'=>'columnList(this)'));
			echo CHtml::endForm();
			echo '</div>';
		}
		else
		{
			throw new CHttpException(404);
		}
	}
}