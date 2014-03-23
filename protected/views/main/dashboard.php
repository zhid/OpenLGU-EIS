<!--This Page is for the Dashboard Page-->
<style type="text/css">
	#main-menu ul li:nth-of-type(1) a {
		border-bottom: 5px solid #bfee32;
	}
</style>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/dashboard.js',CClientScript::POS_HEAD); ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery');?>

<div id="main-flash" style="display: none">
	
</div>

<div id="page-name">
	<?php echo strtoupper($area->area_name); ?> DASHBOARD
</div>

<div id="dashboard-container">
	<div id="dashboard-side">
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
		
		<div id="dashboard-panel">
			<?php
				echo CHtml::beginForm(array('main/querydata'), 'POST', array('name'=>'queryDataForm'));
				echo CHtml::endForm();
			?>
			<div id="reload" class="panel-button"></div>
			<div id="save" class="panel-button"></div>
			<div id="alert" class="panel-button"></div>
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
	</div>
	
	<div id="dashboard-charts">
		<div id="mode">View Mode</div>
		<div id="chart-collapse">
			<form id="chartCollapsable" name="chartCollapsable">
			<select name="collapseType" onchange="chartCollapse(this)">
				<option selected="selected">Collapse</option>
				<option>No Collapse</option>
			</select>
			</form>
		</div>
		<input id="view_mode" type="hidden" value="" />
		<div id="table" type="table" class="charts" ischartselected="true"></div>
		<div id="bar" type="bar" class="charts" ischartselected="false"></div>
		<div id="line" type="line" class="charts" ischartselected="false"></div>
		<div id="pie" type="pie" class="charts" ischartselected="false"></div>
		<div id="bubble" type="bubble" class="charts" ischartselected="false"></div>
		<div id="area" type="area" class="charts" ischartselected="false"></div>
		<div id="map" type="map" class="charts" ischartselected="false"></div>
	</div>
</div>