<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('edituser_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('edituser_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('edituser_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('edituser_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Edit Login Information',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="edit-data-area">		
		<?php echo CHtml::beginForm(array('settings/edituserinfo'), 'post'); ?>
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
				<?php echo CHtml::activePasswordField($model, 'password'); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'password'); ?>
			</div>
			
			<div class="label">
				<?php echo 'Re-type Password:'; ?>
			</div>
			<div class="field">
				<?php echo CHtml::activePasswordField($model, 'retype_password'); ?>
			</div>
			<div class="error-msg">
				<?php echo CHtml::error($model, 'retype_password'); ?>
			</div>
			
			<div id="submit">
				<?php echo CHtml::submitButton('Edit Login Info'); ?>
			</div>
		<?php echo CHtml::endForm(); ?>
	</div>
</div>

<div id="controls">
	<?php if(Yii::app()->user->roles == "admin"):?>
	<div class="portlet-decoration">Settings</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Edit Login Info', 'url'=>array('/settings/edituserinfo')),
				//array('label'=>'Add Users', 'url'=>array('/settings/adduser')),
				//array('label'=>'Delete Users', 'url'=>array('/settings/deleteuser')),
				array('label'=>'Manage Users', 'url'=>array('/settings/adduser')),
				array('label'=>'Areas', 'url'=>array('/settings/listofareas'))
			),
		)); ?>
	</div>
	<?php endif;?>
</div>