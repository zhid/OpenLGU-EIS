<!--This Page is for the Settings Page-->
<style type="text/css">
	#controls ul li:nth-of-type(1) a {
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

<div id="page-name">
	Settings
</div>

<div id="container">
	<div id="controls">
		<div id="control-name"><?php echo $measure->measure_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
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
						''.strtoupper($measure->measure_name),
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
				   '$(".editmeasure-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
					array('label'=>'Edit'),
				),
			)); ?>
			<?php if(Yii::app()->user->hasFlash('editmeasure_success')):?>
				<div class="editmeasure-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('editmeasure_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('editmeasure_failed')):?>
				<div class="editmeasure-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('editmeasure_failed'); ?>
				</div>
			<?php endif;?>
			</div>
			
			<div id="edit-data-area">		
				<?php echo CHtml::beginForm(array('settings/editmeasure?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
					<?php echo CHtml::activeHiddenField($model, 'measure_id', array('value'=>$measure->measure_id)); ?>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'measure_name'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeTextField($model, 'measure_name', array('value'=>$measure->measure_name)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'measure_name'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'threshold'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeNumberField($model, 'threshold', array('min'=>0, 'value'=>$measure->threshold)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'threshold'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'alert_time'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeDropDownList($model, 'alert_time', array('Every week'=>'Every week', 'Every day'=>'Every day', 'Every hour'=>'Every hour'), array('options'=>array(($measure->alert_time)=>array('selected'=>true)))); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'alert_time'); ?>
					</div>
					
					<div class="description-label">
						<?php echo CHtml::activeLabel($model, 'description'); ?>
					</div>
					<div class="description-field">
						<?php echo CHtml::activeTextArea($model, 'description', array('value'=>$measure->description)); ?>
					</div>
					<div class="description-error-msg">
						<?php echo CHtml::error($model, 'description'); ?>
					</div>
					
					<div id="submit">
						<?php echo CHtml::submitButton('Update Measure'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>