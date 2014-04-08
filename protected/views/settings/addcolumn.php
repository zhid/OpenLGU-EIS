<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('addcolumn_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addcolumn_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('addcolumn_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addcolumn_failed'); ?>
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
					''.$measure->measure_name.''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
					'Add Column',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="overview-data">
		<div id="edit-data-area">		
			<?php echo CHtml::beginForm(array('settings/addcolumndimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
				<?php echo CHtml::activeHiddenField($model, 'measure_id', array('value'=>$measure->measure_id)); ?>
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'column_name'); ?>
				</div>
				<div class="field">
					<?php echo CHtml::activeTextField($model, 'column_name'); ?>
				</div>
				<div class="error-msg">
					<?php echo CHtml::error($model, 'column_name'); ?>
				</div>
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'column_data_type'); ?>
				</div>
				<div class="field">
					<?php echo CHtml::activeDropDownList($model, 'column_data_type', array('bigint'=>'integer', 'double precision'=>'float', 'text'=>'text')); ?>
				</div>
				<div class="error-msg">
					<?php echo CHtml::error($model, 'column_data_type'); ?>
				</div>
				
				<div id="submit">
					<?php echo CHtml::submitButton('Add Column'); ?>
				</div>
			<?php echo CHtml::endForm(); ?>
		</div>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration">Dimensions</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Add Row', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Add Column', 'url'=>array('/settings/addcolumndimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
</div>