<?php

Class DatacaptureController extends CController
{
	public $defaultAction = 'capture';
	
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
				'actions'=>array('capture'),
				'users'=>array('?'),
			),
			array ('allow',
				'actions'=>array('capture'),
				'users'=>array('@'),
			),
		);
	}
	
	public function actionCapture()
	{
		$area_array = array();
		$measure_array = array();
		$rows = NULL;
		$columns = NULL;
		
		$model = new DataCapture();
		
		if(!isset($_GET['page']))
		{
			throw new CHttpException(404);
		}
	
		if(!isset($_POST['areaid']) && !isset($_POST['measureid']) && $_GET['page'] == 1)
		{	
			$page = $_GET['page'];
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$areas = Area::model()->findAll($criteria);
			$rows = NULL;
			$columns = NULL;
			
			foreach($areas as $area)
			{
				$area_array[$area->area_id] = $area->area_name;
			}
			
			$this->render('datacapture', array('page'=>$page, 'model'=>$model,'rows'=>$rows, 'columns'=>$columns, 'area_array'=>$area_array, 'measure_array'=>$measure_array));
		}
		else if((isset($_POST['areaid']) || isset(Yii::app()->session['areaid'])) && !isset($_POST['measureid']) && $_GET['page'] == 2)
		{
			$areaid;
			if(!isset($_POST['areaid']))
			{
				$areaid = Yii::app()->session['areaid'];
			}
			else
			{
				$areaid = $_POST['areaid'];
				Yii::app()->session['areaid'] = $_POST['areaid'];
			}
		
			$page = $_GET['page'];
			$criteria = new CDbCriteria();
			$criteria->select = 'measure_id, measure_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$areaid);
			$measures = Measure::model()->findAll($criteria);
			$rows = NULL;
			$columns = NULL;
			
			foreach($measures as $measure)
			{
				$measure_array[$measure->measure_id] = $measure->measure_name;
			}
		
			$this->render('datacapture', array('page'=>$page, 'model'=>$model, 'rows'=>$rows, 'columns'=>$columns, 'area_array'=>$area_array, 'measure_array'=>$measure_array));
		}
		else if(isset(Yii::app()->session['areaid']) && (isset($_POST['measureid']) || isset(Yii::app()->session['measureid'])) && $_GET['page'] == 3)
		{
			if(isset($_POST['measureid']))
			{
				Yii::app()->session['measureid'] = $_POST['measureid'];
			}
			$rows = NULL;
			$columns = NULL;
			
			$page = $_GET['page'];
			$this->render('datacapture', array('page'=>$page, 'model'=>$model, 'rows'=>$rows, 'columns'=>$columns, 'area_array'=>$area_array, 'measure_array'=>$measure_array));
		}
		else if((isset($_POST['numberofentries']) || isset(Yii::app()->session['numberofentries'])) && $_GET['page'] == 4)
		{
			$page = $_GET['page'];
			
			if(isset($_POST['numberofentries']))
			{
				$entries = $_POST['numberofentries'];
				Yii::app()->session['numberofentries'] = $_POST['numberofentries'];
			}
			else
			{
				$entries = Yii::app()->session['numberofentries'];
			}
			
			$dimensions = array();
			$i = 0;
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>Yii::app()->session['measureid']);
			$count_rows = RowDimension::model()->count($criteria);
			$rows = RowDimension::model()->findAll($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id';
			$criteria->params = array(':measure_id'=>Yii::app()->session['measureid']);
			$count_columns = ColumnDimension::model()->count($criteria);
			$columns = ColumnDimension::model()->findAll($criteria);
			
			foreach($rows as $row)
			{	
				$dimensions[$i++] = preg_replace('/\s+/', '_', strtolower($row->row_name));
			}
			foreach($columns as $column)
			{
				$dimensions[$i++] = preg_replace('/\s+/', '_', strtolower($column->column_name));
			}
			
			$insert_dim =  '('.implode(",", $dimensions).')';
			
			$count = ($count_rows + $count_columns) * $entries;
			
			$model = array();
			for($i=0; $i<$count; $i++)
			{
				$model[$i] = new DataCapture();
			}
			
			if(isset($_POST['DataCapture']))
			{
				$valid = TRUE;
				$count = count($_POST['DataCapture']);
				
				for($i=0; $i<$count; $i++)
				{
					$model[$i]->field = $_POST['DataCapture'][$i]['field'];
					$model[$i]->field_name = $_POST['DataCapture'][$i]['field_name'];
					$model[$i]->data_type = $_POST['DataCapture'][$i]['data_type'];
					$valid = $model[$i]->validate() && $valid;
				}						
			
				$data = array();
				$insert_data = array();
				$insert_into = '';
				$per_data = 0;
				$per_insert = 0;
				
				if($valid)
				{
					for($j=0; $j<count($_POST['DataCapture']); $j++)
					{
						if($model[$j]->data_type == 'text')
						{
							$data[$per_data] = "'".$model[$j]->field."'";
						}
						else
						{
							$data[$per_data] = $model[$j]->field;
						}
						$per_data++;
					
						if($per_data == (count($rows)+count($columns)))
						{
							$insert_data[$per_insert] = '('.implode(",", $data).')';
							$per_insert++;
							$per_data = 0;
						}
					}
					$insert_into = implode(",", $insert_data);
					
					$criteria = new CDbCriteria();
					$criteria->select = 'measure_name';
					$criteria->condition = 'measure_id=:measure_id';
					$criteria->params = array(':measure_id'=>Yii::app()->session['measureid']);
					$measure = Measure::model()->find($criteria);
					$measure_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
					
					try {
						$sql_insert = "INSERT INTO $measure_name $insert_dim VALUES $insert_into";
						$result = Yii::app()->db->createCommand($sql_insert)->execute();
						
						Yii::app()->user->setFlash('datacapture_success', "Data has been Added!");
						
						$url = $this->createUrl('datacapture/capture', array('page'=>1), '&');
						$this->redirect($url);
					} catch(Exception $exception) {
						Yii::app()->user->setFlash('datacapture_failed', "Adding data failed!");
						//echo $exception;
					}
				}
			}
			
			$this->render('datacapture', array('page'=>$page, 'model'=>$model, 'rows'=>$rows, 'columns'=>$columns, 'area_array'=>$area_array, 'measure_array'=>$measure_array));
		}
	}
}