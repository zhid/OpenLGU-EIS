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

<div id="page-name">
	Settings
</div>

<div id="container">
	<div id="controls">
		<div id="control-name">SETTINGS</div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Areas', 'url'=>array('/settings/listofareas')),
				array('label'=>'Add New Area', 'url'=>array('/settings/addnewarea/'))
			),
		)); ?>
	</div>
	
	<div id="add-form-container">
		<div id="add-form-name">
			ADD NEW AREA
		</div>
		
		<?php
			/*Fade out Effect for Success Message in Add New Area*/
			Yii::app()->clientScript->registerScript(
			   'myHideEffect',
			   '$(".addarea-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
			   CClientScript::POS_READY
			);
		?>
		<?php if(Yii::app()->user->hasFlash('addarea_success')):?>
			<div class="addarea-flash-message" id="success-flash">
				<?php echo Yii::app()->user->getFlash('addarea_success'); ?>
			</div>
		<?php endif; ?>
		<?php if(Yii::app()->user->hasFlash('addarea_failed')):?>
			<div class="addarea-flash-message" id="failed-flash">
				<?php echo Yii::app()->user->getFlash('addarea_failed'); ?>
			</div>
		<?php endif; ?>
		
		<div id="add-form">
			<?php echo CHtml::beginForm(array('settings/addnewarea'), 'post', array('enctype'=>'multipart/form-data')); ?>
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'area_logo'); ?>
				</div>
				<div class="field">
					<?php echo CHtml::activeFileField($model, 'area_logo'); ?>
				</div>
				<div class="error-msg">
					<!--Returns an error message if the managing office field is black-->
					<?php echo CHtml::error($model, 'area_logo'); ?>
				</div>
				
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'area_name'); ?>
				</div>	
				<div class="field">
					<?php echo CHtml::activeTextField($model, 'area_name'); ?>
				</div>
				<div class="error-msg">
					<!--Returns an error message if the areaname field is black-->
					<?php echo CHtml::error($model, 'area_name'); ?>
				</div>
				
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'managing_office'); ?>
				</div>
				<div class="field">
					<?php echo CHtml::activeTextField($model, 'managing_office'); ?>
				</div>
				<div class="error-msg">
					<!--Returns an error message if the managing office field is black-->
					<?php echo CHtml::error($model, 'managing_office'); ?>
				</div>
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'officer_in_charge'); ?>
				</div>
				<div class="field">
					<?php echo CHtml::activeTextField($model, 'officer_in_charge'); ?>
				</div>
				<div class="error-msg">
					<!--Returns an error message if the managing office field is black-->
					<?php echo CHtml::error($model, 'officer_in_charge'); ?>
				</div>
			
				<div id="submit">
					<?php echo CHtml::submitButton('Add New Area'); ?>
				</div>
			<?php echo CHtml::endForm(); ?>
		</div>
	</div>
</div>