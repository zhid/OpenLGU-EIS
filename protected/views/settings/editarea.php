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
		<div id="control-name"><?php echo $area->area_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/areaoverview?areaid='.$area_id)),
				array('label'=>'List of Measures', 'url'=>array('/settings/listofmeasures?areaid='.$area_id)),
				array('label'=>'Add New Measure', 'url'=>array('/settings/addnewmeasure?areaid='.$area_id.'&page=1'))
			),
		)); ?>
	</div>
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area->area_name),
					),
					'homeLink'=>false,
				));
			?>
		</div>
		
		<div id="overview-data">
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Overview', 'url'=>array('/settings/areaoverview?areaid='.$area_id)),
					array('label'=>'Edit')
				),
			)); ?>
			</div>
			
			<?php
				/*Fade out Effect for Success Message in Edit Area*/
				Yii::app()->clientScript->registerScript(
				   'myHideEffect',
				   '$(".editarea-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<?php if(Yii::app()->user->hasFlash('editarea_success')):?>
				<div class="editarea-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('editarea_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('editarea_failed')):?>
				<div class="editarea-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('editarea_failed'); ?>
				</div>
			<?php endif;?>
			
			<div id="edit-data-area">		
				<?php echo CHtml::beginForm(array('settings/editarea?areaid='.$area_id), 'post', array('enctype'=>'multipart/form-data')); ?>
					<?php echo CHtml::activeHiddenField($model, 'area_id', array('value'=>$area->area_id)); ?>
					
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
						<?php echo CHtml::activeTextField($model, 'area_name', array('value'=>$area->area_name)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'area_name'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'managing_office'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeTextField($model, 'managing_office', array('value'=>$area->managing_office)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'managing_office'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'officer_in_charge'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeTextField($model, 'officer_in_charge', array('value'=>$area->officer_in_charge)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'officer_in_charge'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'visible'); ?>
					</div>
					<div class="field">
						<?php 
							if($area->visible)
							{
								$checked = "checked";
							}
							else{
								$checked = "";
							}
						?>
						<?php echo CHtml::activeCheckBox($model, 'visible', array('checked'=>$checked)); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'visible'); ?>
					</div>
					
					<div id="submit">
						<?php echo CHtml::submitButton('Update Area'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>