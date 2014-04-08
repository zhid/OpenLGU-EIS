<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('editmeasure_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('editmeasure_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('editmeasure_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('editmeasure_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/edituserinfo'),
					'Areas'=>array('settings/listofareas'),
					''.$area->area_name.''=>array('settings/areaoverview?areaid='.$area->area_id),
					''.$measure->measure_name,
				),
				'homeLink'=>false,
			));
		?>
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

<div id="controls">
	<div class="portlet-decoration"><?php echo $measure->measure_name; ?></div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Edit Measure', 'url'=>array('/settings/editmeasure?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Dimensions', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Hierarchies', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
</div>