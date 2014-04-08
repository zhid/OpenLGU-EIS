<?php

Class SettingsController extends CController
{
	public $defaultAction = 'adduser';
	public $breadcrumbs;
	
	public function filters()
	{
		return array (
			'accessControl',
		);
	}
	
	public function accessRules()
	{
		return array (
			array ('allow',
				'actions'=>array('listofareas', 'searcharea', 'addnewarea', 'areaoverview', 'editarea', 'addnewmeasure', 'removeformsession', 'createmeasure', 'listofmeasures', 'searchmeasure','measureoverview', 'editmeasure', 'addrowdimension', 'addcolumndimension', 'deletearea', 'deletemeasure', 'createcolumnhierarchy', 'createrowhierarchy',
					'listthresholds', 'addthresholds', 'deletethreshold', 'deleteUser', 'adduser'),
				'roles'=>array('admin'),
			),
			array ('deny',
				'actions'=>array('listofareas', 'searcharea', 'addnewarea', 'areaoverview', 'editarea', 'addnewmeasure', 'removeformsession', 'createmeasure', 'listofmeasures', 'searchmeasure','measureoverview', 'editmeasure', 'addrowdimension', 'addcolumndimension', 'deletearea', 'deletemeasure', 'createcolumnhierarchy', 'createrowhierarchy',
					'listthresholds', 'addthresholds', 'deletethreshold', 'deleteUser', 'adduser'),
				'roles'=>array('LCE', 'dataencoder'),
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
			$model->service_area = $_POST['AddArea']['service_area'];
			$model->area_logo = $_POST['AddArea']['area_logo'];
			
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
					$area->service_area = $model->service_area;
					$area->area_logo = $model->area_logo;
					
					if($area->save())
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
				catch(Exception $exception) {
					//catches an exception, if save intervened by another request
					$transaction->rollback();
					Yii::app()->user->setFlash('addarea_failed', "Adding new area failed!");
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
				$criteria->select = 'area_id, area_name, managing_office, officer_in_charge, service_area';
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
			$criteria->select = 'area_id, area_name, managing_office, officer_in_charge, visible, service_area, area_logo';
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
			$model->service_area = $_POST['EditArea']['service_area'];
			$model->area_logo = $_POST['EditArea']['area_logo'];
			
			$area_model = Area::model();
			$transaction = $area_model->dbConnection->beginTransaction();
			try {
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'area_id=:area_id';
				$criteria->params = array(':area_id'=>$_GET['areaid']);
				
				$area = $area_model->find($criteria);
				
				if($area !== NULL)
				{
					if($model->validate())
					{
						$area->area_name = $model->area_name;
						$area->managing_office = $model->managing_office;
						$area->officer_in_charge = $model->officer_in_charge;
						$area->visible = $model->visible;
						$area->service_area = $model->service_area;
						$area->area_logo = $model->area_logo;
						
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
						$model->measure_name = $_POST['AddMeasureFirstForm']['measure_name'];
						$model->number_of_rows = $_POST['AddMeasureFirstForm']['number_of_rows'];
						$model->number_of_columns = $_POST['AddMeasureFirstForm']['number_of_columns'];
						$model->description = $_POST['AddMeasureFirstForm']['description'];
						
						if($model->validate())
						{
							Yii::app()->session['measure_name'] = $model->measure_name;
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
								//$this->createmeasure($_GET['areaid']);
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
		
		$sess_var = array('measure_name', 'number_of_rows', 'number_of_columns', 'description');
		//removes session variables for measure's name, data type and number of rows and column
		for($i=0; $i<4; $i++)
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
			$description = Yii::app()->session['description'];
			$measure_area_id = $area_id;
			$add_measure_command = Yii::app()->db->createCommand();
			$add_measure_command->insert('measure', array('measure_name'=>$measure_name, 'area_id'=>$measure_area_id, 'description'=>$description));
			
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
			
			$begin_trigger_measure_name = preg_replace('/\s+/', '_', strtolower($measure_name));
			$begin_trigger = "CREATE OR REPLACE FUNCTION $begin_trigger_measure_name"."_trigger_function() \n
						RETURNS TRIGGER AS $$ \n
						BEGIN \n
						RAISE NOTICE 'Trigger Created!'; \n";
			$end_trigger = "RETURN NEW \n;
						END \n;
						$$ LANGUAGE plpgsql; \n";
						
			$trigger_function_sql = "$begin_trigger \n $end_trigger \n";
			$return = Yii::app()->db->createCommand($trigger_function_sql)->execute();

			$trigger_sql = 'CREATE TRIGGER '.preg_replace('/\s+/', '_', strtolower($measure_name)).'_trigger
					AFTER INSERT ON '.preg_replace('/\s+/', '_', strtolower($measure_name)).'
					FOR EACH ROW EXECUTE PROCEDURE '.preg_replace('/\s+/', '_', strtolower($measure_name)).'_trigger_function()';
			$return = Yii::app()->db->createCommand($trigger_sql)->execute();
			
			
			$transaction->commit();
			return TRUE;
		}
		catch(Exception $exception) {
			$transaction->rollback();
			//echo $exception;
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
			$criteria->select = 'measure_id, measure_name, alert_level';
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
			$criteria->select = '*';
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
						echo $exception;
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
					$trigger_function_names = array();
					
					//find all measures of the current area
					$i = 0;
					foreach($measures as $measure)
					{
						$trigger_function_names[$i] = preg_replace('/\s+/', '_', strtolower($measure->measure_name)).'_trigger_function()';
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
					
					for($trig_count=0; $trig_count<count($trigger_function_names); $trig_count++)
					{
						$drop_function_sql = "DROP FUNCTION $trigger_function_names[$trig_count]";
						$return = Yii::app()->db->createCommand($drop_function_sql)->execute();
					}
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
						
						//delete thresholds
						$criteria = new CDbCriteria();
						$criteria->addInCondition('column_id', $column_id_array);
						Threshold::model()->deleteAll($criteria);
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
					
					$trigger_measure_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
					
					//delete measure
					$measure->delete();
					
					$drop_function_sql = "DROP FUNCTION $trigger_measure_name"."_trigger_function()";
					$return = Yii::app()->db->createCommand($drop_function_sql)->execute();
					
					$transaction->commit();
					Yii::app()->user->setFlash('deletemeasure_success', "Measure has been deleted!");
				} catch(Exception $exception) {
					$transaction->rollback();
					Yii::app()->user->setFlash('deletemeasure_failed', $exception);
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
									$isBottom = true;
									
									for($j=0; $j<count($_POST['CreateColumnHierarchy']); $j++)
									{
										if(($_POST['CreateColumnHierarchy'][$i]['category_id'] == $_POST['CreateColumnHierarchy'][$j]['parent_id']) && ($i != $j))
										{
											$isBottom = false;
											break;
										}
									}
								}
								else
								{
									$isBottom = true;
									for($j=0; $j<count($_POST['CreateColumnHierarchy']); $j++)
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
									$isBottom = true;
									
									for($j=0; $j<count($_POST['CreateRowHierarchy']); $j++)
									{
										if(($_POST['CreateRowHierarchy'][$i]['category_id'] == $_POST['CreateRowHierarchy'][$j]['parent_id']) && ($i != $j))
										{
											$isBottom = false;
											break;
										}
									}
								}
								else
								{
									$isBottom = true;
									for($j=0; $j<count($_POST['CreateRowHierarchy']); $j++)
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
	
	public function actionListthresholds()
	{
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
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
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->params = array(':measure_id'=>$_GET['measureid']);
				
				$count = Threshold::model()->count($criteria);
				$pages = new CPagination($count);
				$pages->pageSize = 4;
				$pages->applyLimit($criteria);
				$thresholds = Threshold::model()->findAll($criteria);
				$column_name = array();
				$i = 0;
				
				foreach($thresholds as $threshold)
				{
					$criteria = new CDbCriteria();
					$criteria->select = 'column_name';
					$criteria->condition = 'column_id=:column_id';
					$criteria->params = array(':column_id'=>$threshold->column_id);
					$column = ColumnDimension::model()->find($criteria);
					
					$column_name[$i++] = $column->column_name;
				}
			
				
				$this->render('listofindicators', array('area'=>$area, 'measure'=>$measure, 'column_name'=>$column_name, 'thresholds'=>$thresholds, 'pages'=>$pages, 'count'=>$count));
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
	
	public function actionAddthresholds()
	{
		if(isset($_GET['areaid']) && isset($_GET['measureid']))
		{
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
				$model = new AddThreshold();
				
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->params = array(':measure_id'=>$_GET['measureid']);
				$columns = ColumnDimension::model()->findAll($criteria);
				
				$column_id_array = array();
				foreach($columns as $column)
				{
					$column_id_array[$column->column_id] = $column->column_name;
				}
			
				if(isset($_POST['AddThreshold']))
				{
					$model->measure_id = $_POST['AddThreshold']['measure_id'];
					$model->column_id = $_POST['AddThreshold']['column_id'];
					$model->lowthreshold = $_POST['AddThreshold']['lowthreshold'];
					$model->highthreshold = $_POST['AddThreshold']['highthreshold'];
					$model->highthreshold_operator = $_POST['AddThreshold']['highthreshold_operator'];
					$model->lowthreshold_operator = $_POST['AddThreshold']['lowthreshold_operator'];
					$model->threshold_type = $_POST['AddThreshold']['threshold_type'];
					
					if($model->validate())
					{
						$transaction = Yii::app()->db->beginTransaction();
						
						$criteria = new CDbCriteria();
						$criteria->select = 'row_name';
						$criteria->condition = 'measure_id=:measure_id';
						$criteria->params = array(':measure_id'=>$measure->measure_id);
						$threshold_rows = RowDimension::model()->findAll($criteria);
						$row_names = "";
						
						$criteria = new CDbCriteria();
						$criteria->select = '*';
						$criteria->condition = 'column_id=:column_id AND threshold_type=:threshold_type';
						$criteria->params = array(':column_id'=>$model->column_id, ':threshold_type'=>$model->threshold_type);
						$thresholds = Threshold::model()->find($criteria);
						
						foreach($threshold_rows as $threshold_row)
						{
							$name = preg_replace('/\s+/', '_', strtolower($threshold_row->row_name));
							$row_names = "$row_names 'for', NEW.$name, ";
						}
						
						if($thresholds != NULL)
						{
							try {
								$thresholds->measure_id = $model->measure_id;
								$thresholds->column_id = $model->column_id;
								$thresholds->lowthreshold = $model->lowthreshold;
								$thresholds->highthreshold = $model->highthreshold;
								$thresholds->lowthreshold_operator = $model->lowthreshold_operator;
								$thresholds->highthreshold_operator = $model->highthreshold_operator;
								$thresholds->threshold_type = $model->threshold_type;
							
								if($thresholds->save())
								{
									$transaction->commit();
									Yii::app()->user->setFlash('addthreshold_success', "Threshold has been added!");
									
									$criteria = new CDbCriteria();
									$criteria->select = '*';
									$criteria->condition = 'column_id=:column_id';
									$criteria->params = array(':column_id'=>$model->column_id);
									$current_thresholds = Threshold::model()->findAll($criteria);
									
									$trigger_condition = "";
									foreach($current_thresholds as $current_threshold)
									{
										$criteria = new CDbCriteria();
										$criteria->select = 'column_name';
										$criteria->condition = 'column_id=:column_id';
										$criteria->params = array(':column_id'=>$current_threshold->column_id);
										$threshold_column = ColumnDimension::model()->find($criteria);
										$column_label_name = $threshold_column->column_name;
										$column_name = preg_replace('/\s+/', '_', strtolower($threshold_column->column_name));
									
										if($current_threshold->highthreshold != NULL)
										{
											$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold AND NEW.$column_name $current_threshold->highthreshold_operator $current_threshold->highthreshold
											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measure->measure_id, $model->column_id, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
										}
										else
										{
											$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold
											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measure->measure_id, $model->column_id, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
										}
									}
									
									$begin_trigger_measure_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
									$begin_trigger = "CREATE OR REPLACE FUNCTION $begin_trigger_measure_name"."_trigger_function() \n
												RETURNS TRIGGER AS $$ \n
												BEGIN \n";
									$end_trigger = "RETURN NEW \n;
												END \n;
												$$ LANGUAGE plpgsql; \n";
												
									$trigger_function_sql = "$begin_trigger \n $trigger_condition \n $end_trigger \n";
									$return = Yii::app()->db->createCommand($trigger_function_sql)->execute();
									$this->refresh();
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('addthreshold_failed', "Adding threshold failed!");
								}
							} catch (Exception $exception) {
								$transaction->rollback();
								Yii::app()->user->setFlash('addthrehold_failed', "Adding threshold failed!");
								//echo $exception;
							}
						}
						else
						{
							try {
								$threshold = new Threshold;
								
								$threshold->measure_id = $model->measure_id;
								$threshold->column_id = $model->column_id;
								$threshold->lowthreshold = $model->lowthreshold;
								$threshold->highthreshold = $model->highthreshold;
								$threshold->lowthreshold_operator = $model->lowthreshold_operator;
								$threshold->highthreshold_operator = $model->highthreshold_operator;
								$threshold->threshold_type = $model->threshold_type;
								
								if($threshold->save())
								{
									$transaction->commit();
									Yii::app()->user->setFlash('addthreshold_success', "Threshold has been added!");
									
									$criteria = new CDbCriteria();
									$criteria->select = '*';
									$criteria->condition = 'column_id=:column_id';
									$criteria->params = array(':column_id'=>$model->column_id);
									$current_thresholds = Threshold::model()->findAll($criteria);
									
									$trigger_condition = "";
									foreach($current_thresholds as $current_threshold)
									{
										$criteria = new CDbCriteria();
										$criteria->select = 'column_name';
										$criteria->condition = 'column_id=:column_id';
										$criteria->params = array(':column_id'=>$current_threshold->column_id);
										$threshold_column = ColumnDimension::model()->find($criteria);
										$column_label_name = $threshold_column->column_name;
										$column_name = preg_replace('/\s+/', '_', strtolower($threshold_column->column_name));
									
										if($current_threshold->highthreshold != NULL)
										{
											$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold AND NEW.$column_name $current_threshold->highthreshold_operator $current_threshold->highthreshold
											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measure->measure_id, $model->column_id, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
										}
										else
										{
											$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold
											) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measure->measure_id, $model->column_id, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
										}
									}
									
									$begin_trigger_measure_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
									$begin_trigger = "CREATE OR REPLACE FUNCTION $begin_trigger_measure_name"."_trigger_function() \n
												RETURNS TRIGGER AS $$ \n
												BEGIN \n";
									$end_trigger = "RETURN NEW \n;
												END \n;
												$$ LANGUAGE plpgsql; \n";
												
									$trigger_function_sql = "$begin_trigger \n $trigger_condition \n $end_trigger \n";
									$return = Yii::app()->db->createCommand($trigger_function_sql)->execute();
									$this->refresh();
								}
								else
								{
									$transaction->rollback();
									Yii::app()->user->setFlash('addthrehold_failed', "Adding threshold failed!");
								}
							} catch (Exception $exception) {
								$transaction->rollback();
								Yii::app()->user->setFlash('addthrehold_failed', "Adding threshold failed!");
								//echo $exception;
							}
						}
					}
				}
				$this->render('addthreshold', array('area'=>$area, 'measure'=>$measure, 'model'=>$model, 'column_id_array'=>$column_id_array));
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
	
	public function actionDeletethreshold()
	{
		if(isset($_POST['thresholdid']) && isset($_POST['columnid']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'area_id, area_name';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$_POST['areaid']);
			$area = Area::model()->find($criteria);
			
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'measure_id=:measure_id AND area_id=:area_id';
			$criteria->order = 'measure_id ASC';
			$criteria->params = array(':measure_id'=>$_POST['measureid'], ':area_id'=>$_POST['areaid']);
			$measure = Measure::model()->find($criteria);
		
			$transaction = Yii::app()->db->beginTransaction();
		
			try {
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'threshold_id=:threshold_id';
				$criteria->params = array(':threshold_id'=>$_POST['thresholdid']);
				Threshold::model()->deleteAll($criteria);
				
				$criteria = new CDbCriteria();
				$criteria->select = 'row_name';
				$criteria->condition = 'measure_id=:measure_id';
				$criteria->params = array(':measure_id'=>$_POST['measureid']);
				$threshold_rows = RowDimension::model()->findAll($criteria);
				$row_names = "";
				
				foreach($threshold_rows as $threshold_row)
				{
					$name = preg_replace('/\s+/', '_', strtolower($threshold_row->row_name));
					$row_names = "$row_names 'for', NEW.$name, ";
				}
				
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'column_id=:column_id';
				$criteria->params = array(':column_id'=>$_POST['columnid']);
				$current_thresholds = Threshold::model()->findAll($criteria);
				
				$trigger_condition = "";
				foreach($current_thresholds as $current_threshold)
				{
					$criteria = new CDbCriteria();
					$criteria->select = 'column_name';
					$criteria->condition = 'column_id=:column_id';
					$criteria->params = array(':column_id'=>$current_threshold->column_id);
					$threshold_column = ColumnDimension::model()->find($criteria);
					$column_label_name = $threshold_column->column_name;
					$column_name = preg_replace('/\s+/', '_', strtolower($threshold_column->column_name));
					
					$measureid = $_POST['measureid'];
					$columnid = $_POST['columnid'];
				
					if($current_threshold->highthreshold != NULL)
					{
						$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold AND NEW.$column_name $current_threshold->highthreshold_operator $current_threshold->highthreshold
						) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measureid, $columnid, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
					}
					else
					{
						$trigger_condition = "$trigger_condition IF (NEW.$column_name $current_threshold->lowthreshold_operator $current_threshold->lowthreshold
						) THEN INSERT INTO alert (measure_id, column_id, alert_type, description, date) VALUES ($measureid, $columnid, '$current_threshold->threshold_type', concat_ws(' ', '$column_label_name', 'is', NEW.$column_name, $row_names 'which is within the threshold of ', '$current_threshold->threshold_type'), now()); \n END IF; \n";
					}
				}
				
				$begin_trigger_measure_name = preg_replace('/\s+/', '_', strtolower($measure->measure_name));
				$begin_trigger = "CREATE OR REPLACE FUNCTION $begin_trigger_measure_name"."_trigger_function() \n
							RETURNS TRIGGER AS $$ \n
							BEGIN \n";
				$end_trigger = "RETURN NEW \n;
							END \n;
							$$ LANGUAGE plpgsql; \n";
							
				$trigger_function_sql = "$begin_trigger \n $trigger_condition \n $end_trigger \n";
				$return = Yii::app()->db->createCommand($trigger_function_sql)->execute();
				
				$transaction->commit();
				Yii::app()->user->setFlash('deletethreshold_success', "Threshold has been deleted!");
			} catch(Exception $exception) {
				$transaction->rollback();
				Yii::app()->user->setFlash('deletethreshold_failed', "Deleting a threshold failed!");
			}
			$url = $this->createUrl('settings/listthresholds', array('areaid'=>$_POST['areaid'], 'measureid'=>$_POST['measureid']), '&');
			$this->redirect($url);
		}
		else
		{
			throw new CHttpException(404);
		}
	}
	
	function actionEditUser()
	{
	
	}
	
	function actionDeleteUser()
	{
		if(isset($_POST['username']))
		{
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'username=:username';
			$criteria->params = array(':username'=>$_POST['username']);
			$user = UserIdentification::model()->find($criteria);
			$user->delete();
			
			Yii::app()->user->setFlash('deleteuser_success', "User has been deleted!");
		}
		$criteria = new CDbCriteria();
		$criteria->select = '*';
		$criteria->condition = 'role=:role';
		$criteria->params = array(':role'=>'dataencoder');
		
		$count = UserIdentification::model()->count($criteria);
		$pages = new CPagination($count);
		$pages->pageSize = 4;
		$pages->applyLimit($criteria);
		$users = UserIdentification::model()->findAll($criteria);
		$area_array = array();
		$area_count = 0;
		
		foreach($users as $user)
		{
			$criteria = new CDbCriteria();
			$criteria->select = '*';
			$criteria->condition = 'area_id=:area_id';
			$criteria->params = array(':area_id'=>$user->area_id);
			$area = Area::model()->find($criteria);
			
			$area_array[$area_count++] = $area->area_name;
		}
	
		$this->render('deleteuser', array('area_array'=>$area_array, 'users'=>$users, 'pages'=>$pages, 'count'=>$count));
	}
	
	function actionEdituserinfo()
	{
		$model = new EditUser();
		
		if(isset($_POST['EditUser']))
		{
			$model->username = $_POST['EditUser']['username'];
			$model->password = $_POST['EditUser']['password'];
			$model->retype_password = $_POST['EditUser']['retype_password'];
			
			if($model->validate())
			{
				$criteria = new CDbCriteria();
				$criteria->select = '*';
				$criteria->condition = 'userid=:userid';
				$criteria->params = array(':userid'=>Yii::app()->user->userid);
				$user = UserIdentification::model()->find($criteria);
			
				$user->username = $model->username;
				$user->password = $model->password;
				
				if($user->save())
				{
					Yii::app()->user->setFlash('edituser_success', "User information has been edited!");
					$this->refresh();
				}
				else
				{
					Yii::app()->user->setFlash('edituser_failed', "Editing user information failed!");
				}
			}
		}
		else
		{
			$criteria = new CDbCriteria();
			$criteria->select = 'username, password';
			$criteria->condition = 'userid=:userid';
			$criteria->params = array(':userid'=>Yii::app()->user->userid);
			$user = UserIdentification::model()->find($criteria);
		
			$model->username = $user->username;
			$model->password = $user->password;
			$model->retype_password = $user->password;
		}
		
		$this->render('edituser', array('model'=>$model));
	}
	
	function actionAdduser()
	{
		$model = new AddUser();
		
		$criteria = new CDbCriteria();
		$criteria->select = 'area_id, area_name';
		$areas = Area::model()->findAll($criteria);
		$area_array = array();
		
		foreach($areas as $area)
		{
			$area_array[$area->area_id] = $area->area_name;
		}
		
		if(isset($_POST['AddUser']))
		{
			$model->username = $_POST['AddUser']['username'];
			$model->password = $_POST['AddUser']['password'];
			$model->retype_password = $_POST['AddUser']['retype_password'];
			$model->area_id = $_POST['AddUser']['area_id'];
			$model->role = $_POST['AddUser']['role'];
			
			if($model->validate())
			{
				$user = new UserIdentification;
				$user->username = $model->username;
				$user->password = $model->password;
				$user->role = 'dataencoder';
				$user->area_id = $model->area_id;
				$user->role = $model->role;
				
				if($user->save())
				{
					Yii::app()->user->setFlash('adduser_success', "New User has been added!");
					$this->refresh();
				}
				else
				{
					Yii::app()->user->setFlash('adduser_failed', "Adding new user failed!");
				}
			}
		}
		
		$this->render('adduser', array('model'=>$model, 'area_array'=>$area_array));
	}
}