<!--This Page is for the Dashboard Page-->
<style type="text/css">
	#main-menu ul li:nth-of-type(1) a 
	{
		border-bottom: 5px solid #bfee32;
	}
	
	#alert-report
	{
		padding-left: 15px;
		padding-top: 5px;
		font-size: 13px;
		width: 185px;
		height: 25px;
		font-family: Arial;
		font-weight: bold;
		text-decoration: none;
		color: #0066cc;
		cursor: pointer;
	}
	
	#alert-report a
	{
		text-decoration: none;
	}
</style>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/dashboard.js',CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery');?>

<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>

<?php if(Yii::app()->user->hasFlash('main-flash')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('main-flash'); ?>
	</div>
<?php endif;?>

<div id="overview-name">
	<?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>array(
				'Main'=>array('main/servicearea'),
				'Areas of Concern'=>array('/main/panel?servicearea='.$servicearea),
				''.$area->area_name,
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="page-name">
	<?php echo strtoupper($area->area_name); ?> DASHBOARD
</div>

<div id="dashboard-side">
	<?php
		/*echo CHtml::beginForm(array('main/querydata'), 'POST', array('name'=>'queryDataForm'));
			echo CHtml::hiddenField('chartCollapse', '', array('id'=>'chartCollapse'));
			echo CHtml::hiddenField('measureId', '', array('id'=>'measureId'));
			echo CHtml::hiddenField('columnName', '', array('id'=>'columnName'));
			echo CHtml::hiddenField('columnDistanceLevel', '', array('id'=>'columnDistanceLevel'));
			echo CHtml::hiddenField('columnParentId', '', array('id'=>'columnParentId'));
			echo CHtml::hiddenField('columnParentValue', '', array('id'=>'columnParentValue'));
			echo CHtml::hiddenField('rowName', '', array('id'=>'rowName'));
			echo CHtml::hiddenField('rowDistanceLevel', '', array('id'=>'rowDistanceLevel'));
			echo CHtml::hiddenField('rowParentId', '', array('id'=>'rowParentId'));
			echo CHtml::hiddenField('rowParentValue', '', array('id'=>'rowParentValue'));
			echo CHtml::hiddenField('viewMode', '', array('id'=>'viewMode'));
			
			echo '<div id="column_sort_container">';
			echo '</div>';
			
			echo '<div id="row_values_container">';
			echo '</div>';
			
			echo CHtml::submitButton('Save Dashboard');
		echo CHtml::endForm();*/
	?>

	<div id="chart-collapse">
		<form id="chartCollapsable" name="chartCollapsable">
			<select name="collapseType" onchange="chartCollapse(this)">
				<option selected="selected">Collapse</option>
				<option>Do Not Collapse</option>
			</select>
		</form>
	</div>
	
	<div class="dashboard-side-name">Measures</div>
	<div id="measures-container">
		<?php
			$measure_array =  array();
			$i = 0;
			foreach($measures as $measure)
			{
				if($i == 0) {$selected = $measure->measure_id; $i++;}
				$measure_array[$measure->measure_id] = $measure->measure_name;
			}
			echo CHtml::beginForm(array('main/getdimensions'), 'POST', array('name'=>'getDimensionsForm'));
				echo CHtml::dropDownList('measureid', $selected, $measure_array, array('id'=>'measure_list'));
			echo CHtml::endForm();
		?>
	</div>
	
	<div id="dimensions">
		<!--Rows and Column Dimensions-->
	</div>
</div>

<div id="dashboard-view">
	<div id="overlay-progress" style="display:none;">
		Loading ...
	</div>
	
	<?php
		echo CHtml::beginForm(array('main/querydata'), 'POST', array('name'=>'queryDataForm'));
		echo CHtml::endForm();
	?>
	
	<?php
		echo CHtml::beginForm(array('main/summaryreport'), 'POST', array('name'=>'summaryReportForm'));
		echo CHtml::endForm();
	?>
	
	<div id="dashboard-charts">
		<input id="view_mode" type="hidden" value="" />
		<div id="table" type="table" class="charts" ischartselected="true"></div>
		<div id="bar" type="bar" class="charts" ischartselected="false"></div>
		<div id="line" type="line" class="charts" ischartselected="false"></div>
		<div id="pie" type="pie" class="charts" ischartselected="false"></div>
		<div id="area" type="area" class="charts" ischartselected="false"></div>
	</div>
</div>

<div class="dashboard-filter">
	<div id="row-filter-title">Rows:</div>
	<div id="row-filter-buttons"></div>
</div>

<div class="dashboard-filter">
	<div id="column-filter-title">Columns:</div>
	<div id="column-filter-buttons"></div>
</div>

<div id="chart-container"></div>