<style>
	<?php if(count($rows) != 0 && count($columns) != 0):?>
	.span-error
	{
		float: left;
		min-height: 12px;
		font-size: 10px;
		width: <?php echo 800/(count($rows)+count($columns))?>px;
	}
	.add-measure input[type="text"]
	{
		width: <?php echo 800/(count($rows)+count($columns))?>px;
		height: 20px;
	}
	<?php endif; ?>
</style>

<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('datacapture_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('datacapture_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('datacapture_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('datacapture_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-name">
	<?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>array(
				'Data Capture',
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="addmeasure-flash-message-container">
	<div id="page-number-indicator">
		<?php echo 'Page '.$page.' of 4';?>
	</div>
</div>

<div class="add-measure">
<?php 
	$this->renderPartial('datacaptureform', array('page'=>$page, 'model'=>$model, 'rows'=>$rows, 'columns'=>$columns, 'area_array'=>$area_array, 'measure_array'=>$measure_array));
?>
</div>