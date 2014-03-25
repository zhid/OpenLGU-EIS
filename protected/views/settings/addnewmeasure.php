<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('addmeasure_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addmeasure_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('addmeasure_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addmeasure_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/listofareas'),
					''.$area->area_name.''=>array('/settings/areaoverview?areaid='.$area_id),
					'Add New Measure',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="addmeasure-flash-message-container">
		<div id="page-number-indicator">
			<?php echo 'Page '.$page.' of 3';?>
		</div>
	</div>
	
	<div id="add-measure-form">
		<?php 
			$this->renderPartial('addnewmeasureform', array('area'=>$area, 'area_id'=>$area_id, 'page'=>$page,'model'=>$model));
		?>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration">Measures</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Measures', 'url'=>array('/settings/listofmeasures?areaid='.$area_id)),
				array('label'=>'Add New Measure', 'url'=>array('/settings/addnewmeasure?areaid='.$area_id.'&page=1'))
			),
		)); ?>
	</div>
</div>