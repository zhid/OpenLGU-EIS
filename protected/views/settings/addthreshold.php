<!--This Page is for the Settings Page-->
<style type="text/css">
	#controls ul li:nth-of-type(2) a {
		text-decoration: none;
		height: 30px;
		width: auto;
		background: url('<?php echo Yii::app()->request->baseUrl; ?>/images/arrow_black.png') no-repeat right;
		background-size: 20px 30px;
		color: black;
	}
	#main-menu ul li:nth-of-type(3) a {
		border-bottom: 5px solid #bfee32;
	}
</style>

<script>
	window.onload =  function() {
		var column = document.getElementById('AddThreshold_column_id');
		changeAndIf(column);
	}

	function changeAndIf(column)
	{
		var condition = document.getElementById('andindicator');
		var options = column.getElementsByTagName('option');
	
		for(var i=0; i<options.length; i++)
		{
			if(options[i].value == column.value)
			{
				condition.innerHTML = options[i].innerHTML;
				break;
			}
		}
	}

	function changeIcon(level)
	{
		var image = document.getElementById('indicator-icon');
		var folder = image.getAttribute('folder');
		
		if(level.value == 'low threat level')
		{
			image.setAttribute('src', folder+'/images/low-threat.png');
		}
		else if(level.value == 'moderate threat level')
		{
			image.setAttribute('src', folder+'/images/moderate-threat.png');
		}
		else if(level.value == 'high threat level')
		{
			image.setAttribute('src', folder+'/images/high-threat.png');
		}
		else if(level.value == 'low opportunity level')
		{
			image.setAttribute('src', folder+'/images/low-opportunity.png');
		}
		else if(level.value == 'moderate opportunity level')
		{
			image.setAttribute('src', folder+'/images/moderate-opportunity.png');
		}
		else if(level.value == 'high opportunity level')
		{
			image.setAttribute('src', folder+'/images/high-opportunity.png');
		}
	}
</script>

<div id="page-name">
	Settings
</div>

<div id="container">
	<div id="controls">
		<div id="control-name"><?php echo $measure->measure_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Thresholds', 'url'=>array('/settings/listindicators?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Add Dimension', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Create Hierarchy', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area->area_name).''=>array('settings/areaoverview?areaid='.$area->area_id),
						''.strtoupper($measure->measure_name).''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
						'ADD THRESHOLD',
					),
					'homeLink'=>false,
				));
			?>
		</div>
		
		<div id="overview-data">
			<?php
				/*Fade out Effect for Success Message in Edit Area*/
				Yii::app()->clientScript->registerScript(
				   'myHideEffect',
				   '$(".addindicator-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'List of Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
					array('label'=>'Add Threshold'),
				),
			)); ?>
			
			</div>
			
			<?php if(Yii::app()->user->hasFlash('addthreshold_success')):?>
				<div class="addindicator-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('addthreshold_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('addthreshold_failed')):?>
				<div class="addindicator-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('addthreshold_failed'); ?>
				</div>
			<?php endif;?>
			
			<div id="edit-data-area">		
				<?php echo CHtml::beginForm(array('/settings/addthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
					<div class="indicator-label">
						If:
					</div>
					<?php echo CHtml::activeHiddenField($model, 'measure_id', array('value'=>($measure->measure_id))); ?>
					<div class="indicator-data">
						<?php echo CHtml::activeDropDownList($model, 'column_id', $column_id_array, array('onchange'=>'changeAndIf(this)')); ?>
					</div>
					<div id="indicator-operator">
						<?php echo CHtml::activeDropDownList($model, 'lowthreshold_operator', array('='=>'is equal to', '>'=>'is greater than to', '>='=>'is greater than or equal to', '<'=>'is less than to', '<='=>'is less than or equal to')); ?>
					</div>
					<div id="indicator-threshold">
						<?php echo CHtml::activeTextField($model, 'lowthreshold', array('placeholder'=>'low threshold')); ?>
					</div>
					<div id="indicator-threshold-error">
						<?php echo CHtml::error($model, 'lowthreshold'); ?>
					</div>
					
					<div class="indicator-label">
						and:
					</div>
			
					<div id="andindicator">
						
					</div>
					<div id="indicator-operator">
						<?php echo CHtml::activeDropDownList($model, 'highthreshold_operator', array('='=>'is equal to', '>'=>'is greater than to', '>='=>'is greater than or equal to', '<'=>'is less than to', '<='=>'is less than or equal to')); ?>
					</div>
					<div id="indicator-threshold">
						<?php echo CHtml::activeTextField($model, 'highthreshold', array('placeholder'=>'high threshold')); ?>
					</div>
					<div id="indicator-threshold-error">
						<?php echo CHtml::error($model, 'highthreshold'); ?>
					</div>
					
					<div class="indicator-label">
						then:
					</div>
					<div class="indicator-level">
						display a
					</div>
					<div id="indicator-operator">
						<?php echo CHtml::activeDropDownList($model, 'threshold_type', array('low threat level'=>'low threat level', 'moderate threat level'=>'moderate threat level', 'high threat level'=>'high threat level', 'low opportunity level'=>'low opportunity level', 'moderate opportunity level'=>'moderate opportunity level', 'high opportunity level'=>'high opportunity level'), array('onchange'=>'changeIcon(this)')); ?>
					</div>
					
					<div class="indicator-label">
						then:
					</div>
					<div class="indicator-level">
						display alert icon as
					</div>
					<div id="indicator-operator">
						<img folder="<?php echo Yii::app()->request->baseUrl; ?>" id="indicator-icon" src="<?php echo Yii::app()->request->baseUrl; ?>/images/low-threat.png" alt="indicator-icon" style="height:30px; widht:30px;"/>
					</div>
					
					<div class="indicator-submit" id="submit">
						<?php echo CHtml::submitButton('Add Threshold'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>