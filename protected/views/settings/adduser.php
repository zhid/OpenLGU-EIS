<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('adduser_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('adduser_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('adduser_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('adduser_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Add User',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="edit-data-area">		
		<?php echo CHtml::beginForm(array('settings/adduser'), 'post'); ?>
			<div class="label">
				<?php echo CHtml::activeLabel($model, 'username'); ?>
			</div>
			<div class="field">
				<?php echo CHtml::activeTextField($model, 'username'); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'username'); ?>
			</div>
			
			<div class="label">
				<?php echo CHtml::activeLabel($model, 'password'); ?>
			</div>
			<div class="field">
				<?php echo CHtml::activeTextField($model, 'password'); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'password'); ?>
			</div>
			
			<div class="label">
				<?php echo 'Re-type Password:'; ?>
			</div>
			<div class="field">
				<?php echo CHtml::activeTextField($model, 'retype_password'); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'retype_password'); ?>
			</div>
			
			<div class="label">
				<?php echo 'Managing Area:'; ?>
			</div>
			<div class="field">
				<?php echo CHtml::activeDropDownList($model, 'area_id', $area_array); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'area_id'); ?>
			</div>
			
			<div class="label">
				<?php echo 'Role:'; ?>
			</div>
			<div class="field">
				<?php echo CHtml::activeDropDownList($model, 'role', array('LCE'=>'LCE', 'admin'=>'admin', 'dataencoder'=>'dataencoder')); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'role'); ?>
			</div>
			
			<div id="submit">
				<?php echo CHtml::submitButton('Add User'); ?>
			</div>
		<?php echo CHtml::endForm(); ?>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration">Settings</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Add Users', 'url'=>array('/settings/adduser')),
				array('label'=>'Delete Users', 'url'=>array('/settings/deleteuser')),
				array('label'=>'Areas', 'url'=>array('/settings/listofareas'))
			),
		)); ?>
	</div>
</div>