<!--This Page is for the Dashboard Page-->
<style type="text/css">
	#main-menu ul li:nth-of-type(1) a {
		border-bottom: 5px solid #bfee32;
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
	<div id="chart-collapse">
		<form id="chartCollapsable" name="chartCollapsable">
			<select name="collapseType" onchange="chartCollapse(this)">
				<option selected="selected">Collapse</option>
				<option>No Collapse</option>
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
	
	<div id="dashboard-charts">
		<input id="view_mode" type="hidden" value="" />
		<div id="table" type="table" class="charts" ischartselected="true"></div>
		<div id="bar" type="bar" class="charts" ischartselected="false"></div>
		<div id="line" type="line" class="charts" ischartselected="false"></div>
		<div id="pie" type="pie" class="charts" ischartselected="false"></div>
		<div id="area" type="area" class="charts" ischartselected="false"></div>
	</div>
	
	<div id="drill-context-column" style="display:none;">
		<div class="context-menu" id="columnrollupclick">Roll Up</div>
		<div class="context-menu" style="border-top: 1px solid #5b5b5b;" id="columndrilldownclick">Drill Down</div>
	</div>
	
	<div id="drill-context-row" style="display:none;">
		<div class="context-menu" id="rowrollupclick">Roll Up</div>
		<div class="context-menu" style="border-top: 1px solid #5b5b5b;" id="rowdrilldownclick">Drill Down</div>
	</div>
	
	<div class="dashboard-filter">
		<div id="row-filter-title">Rows:</div>
		<div id="row-filter-buttons"></div>
	</div>
	
	<div class="dashboard-filter">
		<div id="column-filter-title">Columns:</div>
		<div id="column-filter-buttons"></div>
	</div>
</div>

<div id="chart-container"></div>