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
				array('label'=>'Indicators', 'url'=>array('/settings/listindicators?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
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
						'ADD INDICATOR',
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
					array('label'=>'List of Indicators', 'url'=>array('/settings/listindicators?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
					array('label'=>'Add Indicator'),
				),
			)); ?>
			
			</div>
			
			<?php if(Yii::app()->user->hasFlash('addindicator_success')):?>
				<div class="addindicator-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('addindicator_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('addindicator_failed')):?>
				<div class="addindicator-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('addindicator_failed'); ?>
				</div>
			<?php endif;?>
			
			<div id="edit-data-area">		
				<?php echo CHtml::beginForm(array('/settings/addindicator?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
					<div class="indicator-label">
						If:
					</div>
					<?php echo CHtml::activeHiddenField($model, 'area_id', array('value'=>($area->area_id))); ?>
					<div class="indicator-data">
						<?php echo CHtml::activeDropDownList($model, 'column_id', $column_id_array); ?>
					</div>
					<div id="indicator-operator">
						<?php echo CHtml::activeDropDownList($model, 'indicator_operator', array('='=>'is equal to', '>'=>'is greater than to', '>='=>'is greater than or equal to', '<'=>'is less than to', '<'=>'is less than or equal to')); ?>
					</div>
					<div id="indicator-threshold">
						<?php echo CHtml::activeTextField($model, 'threshold'); ?>
					</div>
					<div id="indicator-threshold-error">
						<?php echo CHtml::error($model, 'threshold'); ?>
					</div>
					
					<div class="indicator-label">
						then:
					</div>
					<div class="indicator-level" style="background-color: #eeeeee;">
						change alert level to
					</div>
					<div id="indicator-operator">
						<?php echo CHtml::activeDropDownList($model, 'indicator_type', array('low threat level'=>'low threat level', 'moderate threat level'=>'moderate threat level', 'high threat level'=>'high threat level', 'low opportunity level'=>'low opportunity level', 'moderate opportunity level'=>'moderate opportunity level', 'high opportunity level'=>'high opportunity level'), array('onchange'=>'changeIcon(this)')); ?>
					</div>
					
					<div class="indicator-label">
						then:
					</div>
					<div class="indicator-level" style="background-color: #eeeeee;">
						display alert icon as
					</div>
					<div id="indicator-operator">
						<img folder="<?php echo Yii::app()->request->baseUrl; ?>" id="indicator-icon" src="<?php echo Yii::app()->request->baseUrl; ?>/images/low-threat.png" alt="indicator-icon" style="height:30px; widht:30px;"/>
					</div>
					
					<div class="indicator-submit" id="submit">
						<?php echo CHtml::submitButton('Add Indicator'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>