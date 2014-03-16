<?php

Class SettingsController extends CController
{
	public $defaultAction = 'listofareas';
	
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
				'actions'=>array('listofareas', 'addnewarea'),
				'users'=>array('?'),
			),
			array('deny',
				'actions'=>array('search', 'areaoverview', 'editarea', 'addnewmeasure'),
				'users'=>array('?'),
			),
			array('deny',
				'actions'=>array('removeformsession', 'createmeasure'),
				'users'=>array('*'),
			),
			array ('allow',
				'actions'=>array('listofareas', 'search', 'addnewarea', 'areaoverview', 'editarea', 'addnewmeasure'),
				'users'=>array('@'),
			),
		);
	}
	
	public function actionListofareas()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'area_id, area_name, managing_office, officer_in_charge';
		
		$count = Area::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->pageSize = 4;
		$pages->applyLimit($criteria);
		$areas = Area::model()->findAll($criteria);
	
		$this->render('settings', array('keyword'=>'','areas'=>$areas, 'pages'=>$pages, 'count'=>$count));
	}
	
	public function actionSearcharea()
	{
		if(isset($_GET['keyword']))
		{	
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name, managing_office, officer_in_charge';
			$criteria->condition = 'lower(area_name)=:area_name';
			$criteria->params = array(':area_name'=>strtolower($_GET['keyword']));
			$criteria->addSearchCondition('lower(area_name)', strtolower($_GET['keyword']), true, 'OR', 'LIKE');
			
			$count = Area::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = 1;
			$pages->applyLimit($criteria);
			$areas = Area::model()->findAll($criteria);
			
			$this->render('settings', array('keyword'=>$_GET['keyword'], 'areas'=>$areas, 'pages'=>$pages, 'count'=>$count));
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionAddnewarea()
	{
		$model = new AddArea();
		$uploaded = FALSE;
		
		if(isset($_POST['AddArea']))
		{
			//validates the new area parameters then insert new area to the database
			$model->area_name = $_POST['AddArea']['area_name'];
			$model->managing_office = $_POST['AddArea']['managing_office'];
			$model->officer_in_charge = $_POST['AddArea']['officer_in_charge'];
			
			$model->area_logo = CUploadedFile::getInstance($model, 'area_logo');
			
			if($model->validate())
			{
				$area = new Area;
				$transaction = $area->dbConnection->beginTransaction();
				try {
					$criteria = new CDbCriteria();
					$criteria->select = 'last_value';	
					$area_id = AreaIdSequence::model()->find($criteria);
					$area_id_seq = $area_id->last_value;
					
					$area->area_name = $model->area_name ;
					$area->managing_office = $model->managing_office;
					$area->officer_in_charge = $model->officer_in_charge;
					$area->area_logo = ($area_id_seq+1).'.'.$model->area_logo->getExtensionName();
					
					if($area->save())
					{
						$uploaded = $model->area_logo->saveAs(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo);
						if($uploaded)
						{
							$transaction->commit();
							Yii::app()->user->setFlash('addarea_success', "New Area has been added!");
							$this->refresh();
						}
						else
						{
							$transaction->rollback();
							Yii::app()->user->setFlash('addarea_failed', "Adding new area failed!");
						}
					}
					else
					{
						$transaction->rollback();
						Yii::app()->user->setFlash('addarea_failed', "Adding new area failed!");
					}
				}
				catch(Exception $exception) {
					//catches an exception, if save intervened by another request
					$transaction->rollback();
					//Yii::app()->user->setFlash('addarea_failed', "Adding new area failed!");
					echo $exception;
				}
			}
		}
		$this->render('addnewarea', array('model'=>$model));
	}
	
	public function actionAreaoverview()
	{
		if(isset($_GET['areaid']))
		{	
			try {
				//find area active record with areaid parameter
				$criteria = new CDbCriteria();
				$criteria->select = 'area_id, area_name, area_logo, managing_office, officer_in_charge';
				$criteria->condition = 'area_id=:area_id';
				$criteria->params = array(':area_id'=>$_GET['areaid']);
				
				$area = Area::model()->find($criteria);
				if($area !== NULL)
				{
					$this->render('areaoverview', array('area_id'=>$_GET['areaid'], 'area'=>$area));
				}
				else
				{
					//Trying to get property of non-object
					throw new CHttpException(404);
				}
			}
			catch(Exception $exception) {
				//Invalid input syntax
				throw new CHttpException(500);
			}
		}
		else
		{
			//No GET data
			throw new CHttpException(404);
		}
	}
	
	public function actionEditarea()
	{
		$model = new EditArea();
		
		if(isset($_GET['areaid']) && !(isset($_POST['EditArea'])))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name, area_logo, managing_office, officer_in_charge, visible';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			
			$area = Area::model()->find($criteria);
			if($area !== NULL)
			{
				$this->render('editarea', array('area_id'=>$_GET['areaid'], 'area'=>$area, 'model'=>$model));
			}
			else
			{
				throw new CHttpException(404);
			}
		}
		else if(isset($_GET['areaid']) && isset($_POST['EditArea']))
		{
			$model->area_name = $_POST['EditArea']['area_name'];
			$model->managing_office = $_POST['EditArea']['managing_office'];
			$model->officer_in_charge = $_POST['EditArea']['officer_in_charge'];
			$model->visible = $_POST['EditArea']['visible'];
			$model->area_logo = CUploadedFile::getInstance($model, 'area_logo');
			
			//try
			$area_model = Area::model();
			$transaction = $area_model->dbConnection->beginTransaction();
			try {
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'area_id=:area_id';
				$criteria->params = array(':area_id'=>$_GET['areaid']);
				
				$area = $area_model->find($criteria);
				
				$uploaded = TRUE;
				if($area !== NULL)
				{
					if($model->validate())
					{
						$area->area_name = $model->area_name;
						$area->managing_office = $model->managing_office;
						$area->officer_in_charge = $model->officer_in_charge;
						$area->visible = $model->visible;
						
						if($model->area_logo != NULL)
						{
							if(is_file(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo))
							{
								unlink(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo);
							}
							$uploaded = $model->area_logo->saveAs(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo);
						}
						
						if($uploaded === TRUE)
						{
							if($area->save())
							{
								$transaction->commit();
								Yii::app()->user->setFlash('editarea_success', "Area information has been updated!");
							}
							else
							{
								Yii::app()->user->setFlash('editarea_failed', "Editing area information failed!");
								$transaction->rollback();
							}
						}
						else
						{
							Yii::app()->user->setFlash('editarea_failed', "Editing area information failed!");
						}
					}
					$this->render('editarea', array('area_id'=>$_GET['areaid'], 'area'=>$area, 'model'=>$model));
				}
				else
				{
					throw new CHttpException(404);
				}
			}
			catch(Exception $exception) {
				$transaction->rollback();
				throw new CHttpException(500);
			}
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionAddnewmeasure()
	{
		if(isset($_GET['areaid']) && isset($_GET['page']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			
			$area = Area::model()->find($criteria);
			switch($_GET['page'])
			{
				case 1:
					$model = new AddMeasureFirstForm();
					if(isset($_POST['AddMeasureFirstForm']['measure_name']) && isset($_POST['AddMeasureFirstForm']['number_of_rows']) 
					&& isset($_POST['AddMeasureFirstForm']['number_of_columns']) && isset($_POST['AddMeasureFirstForm']['description']))
					{
						$model->measure_data_type = $_POST['AddMeasureFirstForm']['measure_data_type'];
						$model->measure_name = $_POST['AddMeasureFirstForm']['measure_name'];
						$model->number_of_rows = $_POST['AddMeasureFirstForm']['number_of_rows'];
						$model->number_of_columns = $_POST['AddMeasureFirstForm']['number_of_columns'];
						$model->description = $_POST['AddMeasureFirstForm']['description'];
						
						if($model->validate())
						{
							Yii::app()->session['measure_name'] = $model->measure_name;
							Yii::app()->session['measure_data_type'] = $model->measure_data_type;
							Yii::app()->session['number_of_rows'] = $model->number_of_rows;
							Yii::app()->session['number_of_columns'] = $model->number_of_columns;
							Yii::app()->session['description'] = $model->description;
						
							$page = $_GET['page'] + 1;
							$url = $this->createUrl('settings/addnewmeasure', array('areaid'=>$_GET['areaid'], 'page'=>$page), '&');
							$this->redirect($url);
						}
					}
					$this->render('addnewmeasure', array('area'=>$area, 'area_id'=>$_GET['areaid'], 'page'=>$_GET['page'], 'model'=>$model));
					break;
				case 2:
					if(isset(Yii::app()->session['measure_name']) && isset(Yii::app()->session['number_of_rows']) & isset(Yii::app()->session['number_of_columns']))
					{
						$model = array();
						for($i=0; $i<Yii::app()->session['number_of_rows']; $i++)
						{
							$model[$i] = new AddMeasureSecondForm();
						}
					
						if(isset($_POST['AddMeasureSecondForm']))
						{
							$valid = TRUE;
							$count = count($_POST['AddMeasureSecondForm']);
							for($i=0; $i<$count; $i++)
							{
								$model[$i]->row_name = $_POST['AddMeasureSecondForm'][$i]['row_name'];
								$model[$i]->row_data_type = $_POST['AddMeasureSecondForm'][$i]['row_data_type'];
								$valid = $model[$i]->validate() && $valid;
							}						
						
							if($valid)
							{
								Yii::app()->session['isColumnFormNext'] = 'YES';
								for($i=0; $i<$count; $i++)
								{
									Yii::app()->session['row'.$i.'_name'] = $model[$i]->row_name;
									Yii::app()->session['row'.$i.'_data_type'] = $model[$i]->row_data_type;
								}
								
								$page = $_GET['page'] + 1;
								$url = $this->createUrl('settings/addnewmeasure', array('areaid'=>$_GET['areaid'], 'page'=>$page), '&');
								$this->redirect($url);
							}
						}
						$this->render('addnewmeasure', array('area'=>$area, 'area_id'=>$_GET['areaid'], 'page'=>$_GET['page'], 'model'=>$model));
					}
					else
					{
						throw new CHttpException(404);
					}
					break;
				case 3:
					if(isset(Yii::app()->session['isColumnFormNext']))
					{
						$model = array();
						for($i=0; $i<Yii::app()->session['number_of_columns']; $i++)
						{
							$model[$i] = new AddMeasureThirdForm();
						}
						
						if(isset($_POST['AddMeasureThirdForm']))
						{
							$valid = TRUE;
							$count = count($_POST['AddMeasureThirdForm']);
							for($i=0; $i<$count; $i++)
							{
								$model[$i]->column_name = $_POST['AddMeasureThirdForm'][$i]['column_name'];
								$model[$i]->column_data_type = $_POST['AddMeasureThirdForm'][$i]['column_data_type'];
								$valid = $model[$i]->validate() && $valid;
							}	
							
							if($valid)
							{
								for($i=0; $i<$count; $i++)
								{
									Yii::app()->session['column'.$i.'_name'] = $model[$i]->column_name;
									Yii::app()->session['column'.$i.'_data_type'] = $model[$i]->column_data_type;
								}
								
								/*create table for the measure*/
								if($this->createmeasure($_GET['areaid']))
								{
									$this->removeformsession();
									Yii::app()->user->setFlash('addmeasure_success', "New Measure has been added!");
									$page = 1;
									$url = $this->createUrl('settings/addnewmeasure', array('areaid'=>$_GET['areaid'], 'page'=>$page), '&');
									$this->redirect($url);
								}
								else
								{
									Yii::app()->user->setFlash('addmeasure_failed', "Adding new measure failed!");
									$page = 1;
									$url = $this->createUrl('settings/addnewmeasure', array('areaid'=>$_GET['areaid'], 'page'=>$page), '&');
									$this->redirect($url);
								}
							}
						}
						$this->render('addnewmeasure', array('area'=>$area, 'area_id'=>$_GET['areaid'], 'page'=>$_GET['page'], 'model'=>$model));
					}
					else
					{
						throw new CHttpException(404);
					}
					break;
				case 4:
					if(isset($_POST['clear']))
					{
						$this->removeformsession();
						$page = 1;
						$url = $this->createUrl('settings/addnewmeasure', array('areaid'=>$_GET['areaid'], 'page'=>$page), '&');
						$this->redirect($url);
					}
					else
					{
						throw new CHttpException(404);
					}
					break;
				default:
					throw new CHttpException(404);
			}
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function removeformsession()
	{
	
		//removes column dimension session
		if(isset(Yii::app()->session['number_of_columns']))
		{
			for($i=0; $i<Yii::app()->session['number_of_columns']; $i++)
			{
				$name_index = 'column'.$i.'_name';
				$type_index = 'column'.$i.'_data_type';
				if(isset(Yii::app()->session[$name_index]) && isset(Yii::app()->session[$type_index]))
				{
					unset(Yii::app()->session[$name_index]);
					unset(Yii::app()->session[$type_index]);
				}
			}
		}
		
		//removes form's third page flag session
		if(isset(Yii::app()->session['isColumnFormNext']))
		{
			unset(Yii::app()->session['isColumnFormNext']);
		}
		
		//removes row dimension session
		if(isset(Yii::app()->session['number_of_rows']))
		{
			for($i=0; $i<Yii::app()->session['number_of_rows']; $i++)
			{
				$name_index = 'row'.$i.'_name';
				$type_index = 'row'.$i.'_data_type';
				if(isset(Yii::app()->session[$name_index]) && isset(Yii::app()->session[$type_index]))
				{
					unset(Yii::app()->session[$name_index]);
					unset(Yii::app()->session[$type_index]);
				}
			}
		}
		
		$sess_var = array('measure_name', 'measure_data_type', 'number_of_rows', 'number_of_columns', 'description');
		//removes session variables for measure's name, data type and number of rows and column
		for($i=0; $i<5; $i++)
		{
			if(isset(Yii::app()->session[$sess_var[$i]]))
			{
				unset(Yii::app()->session[$sess_var[$i]]);
			}
		}
	}
	
	public function createmeasure($area_id)
	{
		$table = array();
								
		$table_name = preg_replace('/\s+/', '_', strtolower(Yii::app()->session['measure_name']));
		$table['measure_data'] = Yii::app()->session['measure_data_type'];
		for($i=0; $i<Yii::app()->session['number_of_rows']; $i++)
		{
			$row_name = preg_replace('/\s+/', '_', strtolower(Yii::app()->session['row'.$i.'_name']));
			$table[$row_name] = Yii::app()->session['row'.$i.'_data_type'];
		}
		for($i=0; $i<Yii::app()->session['number_of_columns']; $i++)
		{
			$column_name = preg_replace('/\s+/', '_', strtolower(Yii::app()->session['column'.$i.'_name']));
			$table[$column_name] = Yii::app()->session['column'.$i.'_data_type'] ;
		}
		
		$transaction = Yii::app()->db->beginTransaction();
		try {
			$create_measure_table_command = Yii::app()->db->createCommand();
			$create_measure_table_command->createTable($table_name, $table);
			
			//add measure parameter to the measure table
			//rewrite, delete underscores
			$measure_name = Yii::app()->session['measure_name'];
			$measure_data_type = Yii::app()->session['measure_data_type'];
			$description = Yii::app()->session['description'];
			$measure_area_id = $area_id;
			$add_measure_command = Yii::app()->db->createCommand();
			$add_measure_command->insert('measure', array('measure_name'=>$measure_name, 'measure_data_type'=>$measure_data_type, 'area_id'=>$measure_area_id, 'description'=>$description));
			
			$criteria = new CDbCriteria();
			$criteria->select = 'last_value';
			$measure_seq = MeasureMeasureIdSeq::model()->find($criteria);
			$measure_id = $measure_seq->last_value;
			
			$column_hierarchy_data = array();
			$column_hierarchy_index = 0;
			$criteria->select = 'last_value';
			$column_seq = ColumnColumnIdSeq::model()->find($criteria);
			$category_id = $column_seq->last_value;
			
			//add measure's column dimension to the column table
			//create column hierarchy
			if(isset(Yii::app()->session['number_of_columns']))
			{
				$columnData = array();
				for($i=0; $i<Yii::app()->session['number_of_columns']; $i++)
				{
					$name_index = 'column'.$i.'_name';
					$type_index = 'column'.$i.'_data_type';
					if(isset(Yii::app()->session[$name_index]) && isset(Yii::app()->session[$type_index]))
					{
						$columnData[$i] = "('" . $measure_id . "', '" . Yii::app()->session[$name_index] . "', '" . Yii::app()->session[$type_index] . "')";
						$column_hierarchy_data[$column_hierarchy_index++] = "('" . ++$category_id . "', '" . $category_id . "')";
					}
				}
			}
			
			$row_hierarchy_data = array();
			$row_hierarchy_index = 0;
			$criteria->select = 'last_value';
			$row_seq = RowRowIdSeq::model()->find($criteria);
			$category_id = $row_seq->last_value;
			
			//add measure's row dimension to the row table 
			//create row hierarchy
			if(isset(Yii::app()->session['number_of_rows']))
			{
				$rowData = array();
				for($i=0; $i<Yii::app()->session['number_of_rows']; $i++)
				{
					$name_index = 'row'.$i.'_name';
					$type_index = 'row'.$i.'_data_type';
					if(isset(Yii::app()->session[$name_index]) && isset(Yii::app()->session[$type_index]))
					{
						$rowData[$i] = "('" . $measure_id . "', '" . Yii::app()->session[$name_index] . "', '" . Yii::app()->session[$type_index] . "')";
						$row_hierarchy_data[$row_hierarchy_index++] = "('" . ++$category_id . "', '" . $category_id . "')";
					}
				}
			}
			
			//add measure's column attributes into column_dimension table
			$insert_column_query = 'INSERT INTO column_dimension (measure_id, column_name, column_data_type) VALUES' . implode(',', $columnData);
			$insert_column_command = Yii::app()->db->createCommand($insert_column_query);
			$insert_column_command->execute();
			
			//add measure's row attributes into row_dimension table
			$insert_row_query = 'INSERT INTO row_dimension (measure_id, row_name, row_data_type) VALUES' . implode(',', $rowData);
			$insert_row_command = Yii::app()->db->createCommand($insert_row_query);
			$insert_row_command->execute();
			
			//add row hierarchy data into row_hierarchy table
			$row_hierarchy_query = 'INSERT INTO row_hierarchy (category_id, parent_id) VALUES' . implode(',', $row_hierarchy_data);
			$row_hierarchy_command = Yii::app()->db->createCommand($row_hierarchy_query);
			$row_hierarchy_command->execute();
			
			//add column hierarchy data into column_hierarchy table
			$column_hierarchy_query = 'INSERT INTO column_hierarchy (category_id, parent_id) VALUES' . implode(',', $column_hierarchy_data);
			$column_hierarchy_command = Yii::app()->db->createCommand($column_hierarchy_query);
			$column_hierarchy_command->execute();
			
			$transaction->commit();
			return TRUE;
		}
		catch(Exception $exception) {
			$transaction->rollback();
			return FALSE;
		}
	}
	
	public function actionListofmeasures()
	{
		if($_GET['areaid'])
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
		
			$criteria = new CDbCriteria();
			$criteria->select = 'measure_id, measure_name, threshold, alert_level, alert_time';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			
			$count = Measure::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = 4;
			$pages->applyLimit($criteria);
			$measures = Measure::model()->findAll($criteria);
		
			$this->render('listofmeasures', array('area_name'=>$area->area_name, 'area_id'=>$_GET['areaid'],'keyword'=>'','measures'=>$measures, 'pages'=>$pages, 'count'=>$count));
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionSearchmeasure()
	{
		if(isset($_GET['keyword']) && isset($_GET['areaid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = 'measure_name, threshold, alert_level, alert_time';
			$criteria->condition = 'lower(measure_name)=:measure_name';
			$criteria->params = array(':measure_name'=>strtolower($_GET['keyword']), ':area_id'=>$_GET['areaid']);
			$criteria->addSearchCondition('lower(measure_name)', strtolower($_GET['keyword']), true, 'OR', 'LIKE');
			$criteria->addCondition('area_id=:area_id', 'AND');
			
			$count = Measure::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = 4;
			$pages->applyLimit($criteria);
			$measures = Measure::model()->findAll($criteria);
			
			$this->render('listofmeasures', array('area_name'=>$area->area_name, 'area_id'=>$_GET['areaid'],'keyword'=>$_GET['keyword'],'measures'=>$measures, 'pages'=>$pages, 'count'=>$count));
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionMeasureoverview()
	{
		if(isset($_GET['measureid']) && isset($_GET['areaid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
		
			if($measure != NULL && $area != NULL)
			{
				$this->render('measureoverview', array('area'=>$area, 'measure'=>$measure));
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
	
	public function actionEditmeasure()
	{
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
			$model = new EditMeasure();
			
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
		
			if(!isset($_POST['EditMeasure']))
			{
				if($measure != NULL && $area != NULL)
				{
					$this->render('editmeasure', array('model'=>$model, 'area'=>$area, 'measure'=>$measure));
				}
				else
				{
					throw new CHttpException(404);
				}
			}
			else if(isset($_POST['EditMeasure']))
			{
				if($measure != NULL && $area != NULL)
				{
					$model->measure_id = $_POST['EditMeasure']['measure_id'];
					$model->measure_name = $_POST['EditMeasure']['measure_name'];
					$model->measure_data_type = $_POST['EditMeasure']['measure_data_type'];
					$model->threshold = $_POST['EditMeasure']['threshold'];
					$model->alert_time = $_POST['EditMeasure']['alert_time'];
					$model->description = $_POST['EditMeasure']['description'];
				
					$measure_model = Measure::model();
					$measure_transaction = Yii::app()->db->beginTransaction();
					try {
						$criteria = new CDbCriteria();
						$criteria->select = '*';
						$criteria->condition = 'measure_id=:measure_id';
						$criteria->params = array(':measure_id'=>$model->measure_id);
						
						$measure = $measure_model->find($criteria);
						$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
						if($model->validate())
						{
							$measure->measure_name = $model->measure_name;
							$measure->measure_data_type = $model->measure_data_type;
							$measure->threshold = $model->threshold;
							$measure->alert_time = $model->alert_time;
							$measure->description = $model->description;
							
							$new_table_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
							if($measure->save())
							{
								if($table != $new_table_name)
								{
									$update_measure_table_command = Yii::app()->db->createCommand();
									$update_measure_table_command->renameTable($table, $new_table_name);
								}
								
								$measure_transaction->commit();
								Yii::app()->user->setFlash('editmeasure_success', "Editing measure has been updated!");
							}
							else
							{
								$measure_transaction->rollback();
								Yii::app()->user->setFlash('editmeasure_failed', "Editing measure information failed!");
							}
						}
					}
					catch(Exception $exception) {
						$measure_transaction->rollback();
						Yii::app()->user->setFlash('editmeasure_failed', "Editing measure information failed!");
					}
					
					$this->render('editmeasure', array('model'=>$model, 'area'=>$area, 'measure'=>$measure));
				}
				else
				{
					throw new CHttpException(404);
				}
			}
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionAddrowdimension()
	{
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
			$model = new AddRow();
			
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
			
			if($measure != NULL && $area != NULL)
			{
				if(isset($_POST['AddRow']))
				{
					$model->row_name = $_POST['AddRow']['row_name'];
					$model->row_data_type = $_POST['AddRow']['row_data_type'];
					$model->measure_id = $_POST['AddRow']['measure_id'];
					
					$transaction = Yii::app()->db->beginTransaction();
					
					if($model->validate())
					{
						try {
							$row_dimension = new RowDimension;
							$row_dimension->measure_id = $model->measure_id;
							$row_dimension->row_name = $model->row_name;
							$row_dimension->row_data_type = $model->row_data_type;
							
							if($row_dimension->save())
							{
								$row_hierarchy = new RowHierarchy();
								$row_hierarchy->category_id = $row_dimension->row_id;
								$row_hierarchy->parent_id = $row_dimension->row_id;
							
								if($row_hierarchy->save())
								{
									$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
									$column = preg_replace('/\s+/', '_', strtolower($row_dimension->row_name));;
									
									$update_measure_table_command = Yii::app()->db->createCommand();
									$update_measure_table_command->addColumn($table, $column, $row_dimension->row_data_type);
									
									$transaction->commit();
									Yii::app()->user->setFlash('addrow_success', "New row has been added!");
									$this->refresh();
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('addrow_failed', "Adding row dimension failed!");
								}
							}
							else
							{
								$transaction->rollback();
								Yii::app()->user->setFlash('addrow_failed', "Adding row dimension failed!");
							}
						}
						catch (Exception $exception)
						{
							$transaction->rollback();
							Yii::app()->user->setFlash('addrow_failed', "Adding row dimension failed!");
						}
					}
				}
				$this->render('addrow', array('model'=>$model, 'area'=>$area, 'measure'=>$measure));
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
	
	public function actionAddcolumndimension()
	{
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
			$model = new AddColumn();
			
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
			
			if($measure != NULL && $area != NULL)
			{
				if(isset($_POST['AddColumn']))
				{
					$model->column_name = $_POST['AddColumn']['column_name'];
					$model->column_data_type = $_POST['AddColumn']['column_data_type'];
					$model->measure_id = $_POST['AddColumn']['measure_id'];
					
					$transaction = Yii::app()->db->beginTransaction();
					
					if($model->validate())
					{
						try {
							$column_dimension = new ColumnDimension;
							$column_dimension->measure_id = $model->measure_id;
							$column_dimension->column_name = $model->column_name;
							$column_dimension->column_data_type = $model->column_data_type;
							
							if($column_dimension->save())
							{
								$column_hierarchy = new ColumnHierarchy();
								$column_hierarchy->category_id = $column_dimension->column_id;
								$column_hierarchy->parent_id = $column_dimension->column_id;
							
								if($column_hierarchy->save())
								{
									$table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
									$column = preg_replace('/\s+/', '_', strtolower($column_dimension->column_name));;
									
									$update_measure_table_command = Yii::app()->db->createCommand();
									$update_measure_table_command->addColumn($table, $column, $column_dimension->column_data_type);
									
									$transaction->commit();
									Yii::app()->user->setFlash('addcolumn_success', "New column has been added!");
									$this->refresh();
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('addcolumn_failed', "Adding column dimension failed!");
								}
							}
							else
							{
								$transaction->rollback();
								Yii::app()->user->setFlash('addcolumn_failed', "Adding column dimension failed!");
							}
						}
						catch (Exception $exception)
						{
							$transaction->rollback();
							Yii::app()->user->setFlash('addcolumn_failed', "Adding column dimension failed!");
							echo $exception;
						}
					}
				}
				$this->render('addcolumn', array('model'=>$model, 'area'=>$area, 'measure'=>$measure));
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
	
	public function actionDeletearea()
	{
		if(isset($_POST['areaid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_POST['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_POST['areaid']);
			$measures = Measure::model()->findAll($criteria);
			
			$transaction = Yii::app()->db->beginTransaction();
			try {
				if($measures != NULL)
				{
					$measure_id_array = array();
					$measure_tables = array();
					
					//find all measures of the current area
					$i = 0;
					foreach($measures as $measure)
					{
						$measure_id_array[$i] = $measure->measure_id;
						$measure_tables[$i] = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
						$i++;
					}
				
					//find all column dimension id of measure to be deleted
					$criteria = new CDbCriteria();
					$criteria->select = '*';
					$criteria->addInCondition('measure_id', $measure_id_array);
					$columns = ColumnDimension::model()->findAll($criteria);
				
					if($columns != NULL)
					{
						$i = 0;
						foreach($columns as $column)
						{
							$column_id_array[$i++] = $column->column_id;
						}
						
						//delete column hierarchy of current measure
						$criteria = new CDbCriteria();
						$criteria->addInCondition('category_id', $column_id_array);
						RowHierarchy::model()->deleteAll($criteria);
					}
					
					//find all row dimension id of measure to be deleted
					$criteria = new CDbCriteria();
					$criteria->select = '*';
					$criteria->addInCondition('measure_id', $measure_id_array);
					$rows = RowDimension::model()->findAll($criteria);

					if($rows != NULL)
					{
						$i = 0;
						foreach($rows as $row)
						{
							$row_id_array[$i++] = $row->row_id;
						}
					
						//delete row hierarchy of current measure
						$criteria = new CDbCriteria();
						$criteria->addInCondition('category_id', $row_id_array);
						RowHierarchy::model()->deleteAll($criteria);
					}
					
					//delete rows of current measure
					$criteria = new CDbCriteria();
					$criteria->select = '*';
					$criteria->addInCondition('measure_id', $measure_id_array);
					RowDimension::model()->deleteAll($criteria);
					
					//delete columns of current measure
					$criteria = new CDbCriteria();
					$criteria->select = '*';
					$criteria->addInCondition('measure_id', $measure_id_array);
					ColumnDimension::model()->deleteAll($criteria);
					
					for($i=0; $i<count($measure_tables); $i++)
					{
						//drop measure tables
						$drop_measure_command = Yii::app()->db->createCommand();
						$drop_measure_command->dropTable($measure_tables[$i]);
					}
					
					//delete measures
					$criteria = new CDbCriteria();
					$criteria->condition = 'area_id=:area_id';
					$criteria->params = array(':area_id'=>$_POST['areaid']);
					Measure::model()->deleteAll($criteria);
				}
				
				//deletes the logo of the area
				if(is_file(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo))
				{
					unlink(Yii::getPathOfAlias('webroot.images').'/logo/'.$area->area_logo);
				}
				
				//delete area
				if($area->delete())
				{
					Yii::app()->user->setFlash('deletearea_success', "Area has been deleted!");
					$transaction->commit();
				}
				else
				{
					Yii::app()->user->setFlash('deletearea_failed', "Deleting an area failed!");
					$transaction->rollback();
				}
			}
			catch (Exception $exception) {
				Yii::app()->user->setFlash('deletearea_failed', "Deleting an area failed!");
				$transaction->rollback();
			}
			$url = $this->createUrl('settings/listofareas');
			$this->redirect($url);
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	public function actionDeletemeasure()
	{
		if(isset($_POST['areaid']) && isset($_POST['measureid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_POST['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->params = array(':measure_id'=>$_POST['measureid'], ':area_id'=>$_POST['areaid']);
			$measure = Measure::model()->find($criteria);
			$measure_table = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
		
			if($area != NULL && $measure != NULL)
			{
				$transaction = Yii::app()->db->beginTransaction();
				try {
					//find all column dimension id of measure to be deleted
					$criteria = new CDbCriteria();
					$criteria->select = 'column_id';
					$criteria->condition = 'measure_id=:measure_id';
					$criteria->params = array(':measure_id'=>$_POST['measureid']);
					$columns = ColumnDimension::model()->findAll($criteria);
					$column_id_array = array();
					
					if($columns != NULL)
					{
						$i = 0;
						foreach($columns as $column)
						{
							$column_id_array[$i++] = $column->column_id;
						}
						
						//delete column hierarchy of current measure
						$criteria = new CDbCriteria();
						$criteria->addInCondition('category_id', $column_id_array);
						ColumnHierarchy::model()->deleteAll($criteria);
					}
					
					//find all column dimension id of measure to be deleted
					$criteria = new CDbCriteria();
					$criteria->select = 'row_id';
					$criteria->condition = 'measure_id=:measure_id';
					$criteria->params = array(':measure_id'=>$_POST['measureid']);
					$rows = RowDimension::model()->findAll($criteria);
					$row_id_array = array();
					
					if($rows != NULL)
					{
						$i = 0;
						foreach($rows as $row)
						{
							$row_id_array[$i++] = $row->row_id;
						}
					
						//delete row hierarchy of current measure
						$criteria = new CDbCriteria();
						$criteria->addInCondition('category_id', $row_id_array);
						RowHierarchy::model()->deleteAll($criteria);
					}
					
					//delete row of current measure
					RowDimension::model()->deleteAll('measure_id=:measure_id', array(':measure_id'=>$_POST['measureid']));
					
					//delete column of current measure
					ColumnDimension::model()->deleteAll('measure_id=:measure_id', array(':measure_id'=>$_POST['measureid']));
					
					//drop measure table
					$drop_measure_command = Yii::app()->db->createCommand();
					$drop_measure_command->dropTable($measure_table);
					
					//delete measure
					$measure->delete();
					
					$transaction->commit();
					Yii::app()->user->setFlash('deletemeasure_failed', "Measure has been deleted!");
				} catch(Exception $exception) {
					$transaction->rollback();
					Yii::app()->user->setFlash('deletemeasure_failed', "Deleting a measure failed!");
				}
				$url = $this->createUrl('settings/listofmeasures', array('areaid'=>$_POST['areaid']), '&');
				$this->redirect($url);
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
	
	public function actionCreatecolumnhierarchy()
	{	
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
			$model = array();
		
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->order = 'measure_id ASC';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
			
			if($area != NULL && $measure != NULL)
			{
				$column_dimension = array();
				$column_id_array = array();
				$hierarchy_selection = array();
			
				//find all column ids and name of the current measure
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->order = 'column_id ASC';
				$criteria->params = array(':measure_id'=>$_GET['measureid']);
				$columns = ColumnDimension::model()->findAll($criteria);
			
				if($columns != NULL)
				{
					$i = 0;
					foreach($columns as $column)
					{
						$model[$i] = new CreateColumnHierarchy();
						$model[$i]->category_id = $column->column_id;
						
						$column_name[$i] = $column->column_name;
						$column_id_array[$i] = $column->column_id;
						$hierarchy_selection[$column->column_id] = $column->column_name;
						$i++;
					}
					
					$criteria = new CDbCriteria();
					$criteria->select = 'parent_id';
					$criteria->order = 'category_id ASC';
					$criteria->addInCondition('category_id', $column_id_array);
					$column_hierarchies = ColumnHierarchy::model()->findAll($criteria);
					
					$i = 0;
					foreach($column_hierarchies as $column_hierarchy)
					{
						$model[$i]->parent_id = $column_hierarchy->parent_id;
						$i++;
					}
				}
				
				if(isset($_POST['CreateColumnHierarchy']))
				{
					$hierarchy_id = array();
					for($i=0; $i<count($_POST['CreateColumnHierarchy']); $i++)
					{
						$hierarchy_id[$i] = array('id'=>$_POST['CreateColumnHierarchy'][$i]['category_id'],'parent'=>$_POST['CreateColumnHierarchy'][$i]['parent_id']);
					}
					
					$column_hierarchy_model = ColumnHierarchy::model();
					$transaction = $column_hierarchy_model->dbConnection->beginTransaction();
					for($i=0; $i<count($_POST['CreateColumnHierarchy']); $i++)
					{
						$isBottom = false;
						$isTop = false;
						$distance = 0;
						
						$model[$i]->category_id = $_POST['CreateColumnHierarchy'][$i]['category_id'];
						$model[$i]->parent_id = $_POST['CreateColumnHierarchy'][$i]['parent_id'];
						
						if(!$transaction->getActive())
						{
							$transaction = $column_hierarchy_model->dbConnection->beginTransaction();
						}
						
						try {
							if($model[$i]->checkhierarchy($hierarchy_id))
							{
								$criteria = new CDbCriteria();
								$criteria->condition = 'category_id=:category_id';
								$criteria->params = array(':category_id'=>$_POST['CreateColumnHierarchy'][$i]['category_id']);
								$column_hierarchy = $column_hierarchy_model->find($criteria);
								
								if($_POST['CreateColumnHierarchy'][$i]['category_id'] == $_POST['CreateColumnHierarchy'][$i]['parent_id'])
								{
									$isTop = true;
								}
								else
								{
									$isBottom = true;
									for($j=0; $j<count($_POST['CreateColumnHierarchy'][$i]['category_id']); $j++)
									{
										if(($_POST['CreateColumnHierarchy'][$i]['category_id'] == $_POST['CreateColumnHierarchy'][$j]['parent_id']) && ($i != $j))
										{
											$isBottom = false;
											break;
										}
									}
									
									$category = $i;
									$parent = $i;
									while($_POST['CreateColumnHierarchy'][$category]['category_id'] != $_POST['CreateColumnHierarchy'][$parent]['parent_id'])
									{
										for($k=0; $k<count($_POST['CreateColumnHierarchy']); $k++)
										{
											if($_POST['CreateColumnHierarchy'][$k]['category_id'] == $_POST['CreateColumnHierarchy'][$parent]['parent_id'])
											{
												$category = $k;
												$parent = $k;
												$distance++;
												break;
											}
										}
									}
								}
								
								$column_hierarchy->parent_id = $model[$i]->parent_id;
								$column_hierarchy->top_flag = $isTop;
								$column_hierarchy->bottom_flag = $isBottom;
								$column_hierarchy->distance_level = $distance;
								if($column_hierarchy->save())
								{
									$transaction->commit();
									Yii::app()->user->setFlash('createhierarchy_success', "A hierarchy has been created!");
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('createhierarchy_failed', "Creating hierarchy failed!");
								}
							}
						}
						catch(Exception $exception) {
							$transaction->rollback();
							Yii::app()->user->setFlash('createhierarchy_failed', "Creating hierarchy failed!");
						}
					}
				}
				$this->render('columnhierarchy', array('model'=>$model, 'hierarchy_selection'=>$hierarchy_selection, 'column_name'=>$column_name, 'area'=>$area, 'measure'=>$measure));
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
	public function actionCreaterowhierarchy()
	{	
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
			$model = array();
		
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_GET['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->order = 'measure_id ASC';
			$criteria->params = array(':measure_id'=>$_GET['measureid'], ':area_id'=>$_GET['areaid']);
			$measure = Measure::model()->find($criteria);
			
			if($area != NULL && $measure != NULL)
			{
				$row_dimension = array();
				$row_id_array = array();
				$hierarchy_selection = array();
			
				//find all row ids and name of the current measure
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->order = 'row_id ASC';
				$criteria->params = array(':measure_id'=>$_GET['measureid']);
				$rows = RowDimension::model()->findAll($criteria);
			
				if($rows != NULL)
				{
					$i = 0;
					foreach($rows as $row)
					{
						$model[$i] = new CreateRowHierarchy();
						$model[$i]->category_id = $row->row_id;
						
						$row_name[$i] = $row->row_name;
						$row_id_array[$i] = $row->row_id;
						$hierarchy_selection[$row->row_id] = $row->row_name;
						$i++;
					}
					
					$criteria = new CDbCriteria();
					$criteria->select = 'parent_id';
					$criteria->order = 'category_id ASC';
					$criteria->addInCondition('category_id', $row_id_array);
					$row_hierarchies = RowHierarchy::model()->findAll($criteria);
					
					$i = 0;
					foreach($row_hierarchies as $row_hierarchy)
					{
						$model[$i]->parent_id = $row_hierarchy->parent_id;
						$i++;
					}
				}
				
				if(isset($_POST['CreateRowHierarchy']))
				{
					$hierarchy_id = array();
					for($i=0; $i<count($_POST['CreateRowHierarchy']); $i++)
					{
						$hierarchy_id[$i] = array('id'=>$_POST['CreateRowHierarchy'][$i]['category_id'],'parent'=>$_POST['CreateRowHierarchy'][$i]['parent_id']);
					}
					
					$row_hierarchy_model = RowHierarchy::model();
					$transaction = $row_hierarchy_model->dbConnection->beginTransaction();
					for($i=0; $i<count($_POST['CreateRowHierarchy']); $i++)
					{
						$isBottom = false;
						$isTop = false;
						$distance = 0;
						
						$model[$i]->category_id = $_POST['CreateRowHierarchy'][$i]['category_id'];
						$model[$i]->parent_id = $_POST['CreateRowHierarchy'][$i]['parent_id'];
						
						if(!$transaction->getActive())
						{
							$transaction = $row_hierarchy_model->dbConnection->beginTransaction();
						}
						
						try {
							if($model[$i]->checkhierarchy($hierarchy_id))
							{	
								$criteria = new CDbCriteria();
								$criteria->condition = 'category_id=:category_id';
								$criteria->params = array(':category_id'=>$_POST['CreateRowHierarchy'][$i]['category_id']);
								$row_hierarchy = $row_hierarchy_model->find($criteria);
						
								if($_POST['CreateRowHierarchy'][$i]['category_id'] == $_POST['CreateRowHierarchy'][$i]['parent_id'])
								{
									$isTop = true;
								}
								else
								{
									$isBottom = true;
									for($j=0; $j<count($_POST['CreateRowHierarchy'][$i]['category_id']); $j++)
									{
										if(($_POST['CreateRowHierarchy'][$i]['category_id'] == $_POST['CreateRowHierarchy'][$j]['parent_id']) && ($i != $j))
										{
											$isBottom = false;
											break;
										}
									}
									
									$category = $i;
									$parent = $i;
									while($_POST['CreateRowHierarchy'][$category]['category_id'] != $_POST['CreateRowHierarchy'][$parent]['parent_id'])
									{
										for($k=0; $k<count($_POST['CreateRowHierarchy']); $k++)
										{
											if($_POST['CreateRowHierarchy'][$k]['category_id'] == $_POST['CreateRowHierarchy'][$parent]['parent_id'])
											{
												$category = $k;
												$parent = $k;
												$distance++;
												break;
											}
										}
									}
								}
								
								$row_hierarchy->parent_id = $model[$i]->parent_id;
								$row_hierarchy->top_flag = $isTop;
								$row_hierarchy->bottom_flag = $isBottom;
								$row_hierarchy->distance_level = $distance;
								if($row_hierarchy->save())
								{
									$transaction->commit();
									Yii::app()->user->setFlash('createhierarchy_success', "A hierarchy has been created!");
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('createhierarchy_failed', "Creating hierarchy failed!");
								}
							}
						}
						catch(Exception $exception) {
							$transaction->rollback();
							Yii::app()->user->setFlash('createhierarchy_failed', "Creating hierarchy failed!");
						}
					}
				}
				$this->render('rowhierarchy', array('model'=>$model, 'hierarchy_selection'=>$hierarchy_selection, 'row_name'=>$row_name, 'area'=>$area, 'measure'=>$measure));
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
}