<script>
	function showlogo(logo)
	{
		var preview = document.getElementById('area-logo');
		preview.setAttribute("class", logo.value);
	}
</script>

<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('editarea_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('editarea_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('editarea_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('editarea_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/listofareas'),
					''.$area->area_name,
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="overview-data">
		<div id="edit-data-area">		
			<?php echo CHtml::beginForm(array('settings/editarea?areaid='.$area_id), 'post', array('enctype'=>'multipart/form-data')); ?>
				<?php echo CHtml::activeHiddenField($model, 'area_id', array('value'=>$area->area_id)); ?>
				
				<div class="label">
					<?php echo CHtml::activeLabel($model, 'area_logo'); ?>
				</div>	
				<div class="field">
					<?php echo CHtml::activeDropDownList($model, 'area_logo', array('fa fa-home fa-5x'=>'Home', 'fa fa-money fa-5x'=>'Money', 'fa fa-building-o fa-5x'=>'Building', 'fa fa-bar-chart-o fa-5x'=>'Bar Chart', 'fa fa-pencil-square-o fa-5x'=>'Pencil',
					'fa fa-road fa-5x'=>'Road', 'fa fa-shopping-cart fa-5x'=>'Cart', 'fa fa-medkit fa-5x'=>'Med Kit', 'fa fa-leaf fa-5x'=>'Leaf', 'fa fa-wheelchair fa-5x'=>'Wheel Chair', 'fa fa-users fa-5x'=>'User', 'fa fa-phone fa-5x'=>'Phone',
					'fa fa-legal fa-5x'=>'Legal', 'fa fa-cogs fa-5x'=>'Cogs', 'fa fa-tasks fa-5x'=>'Task', 'fa fa-book fa-5x'=>'Book', 'fa fa-thumbs-up fa-5x'=>'Thumbs Up', 'fa fa-flag fa-5x'=>'Flag', 'fa fa-archive fa-5x'=>'Archive'), array('onchange'=>'showlogo(this)', 'options'=>array(''.($area->area_logo).''=>array('selected'=>true)))); ?>
				</div>
				<div class="error-msg">
					<i id="area-logo" class="<?php echo $area->area_logo ?>" style="color: gray;"></i>
				</div>
				
				<div class="label">
				<?php echo CHtml::activeLabel($model, 'service_area'); ?>
				</div>	
				<div class="field">
					<?php echo CHtml::activeDropDownList($model, 'service_area', array('1'=>'Administrative Governance', '2'=>'Social Governance', '3'=>'Economic Governance', '4'=>'Good Governance', '5'=>'Environmental Governance'), array('options'=>array(''.($area->service_area).''=>array('selected'=>true)))); ?>
				</div>
				<div class="error-msg">
					<!--Returns an error message if the areaname field is black-->
					<?php echo CHtml::error($model, 'service_area'); ?>
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

<div id="controls">
	<div class="portlet-decoration"><?php echo $area->area_name; ?></div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/areaoverview?areaid='.$area_id)),
				array('label'=>'Edit Area', 'url'=>array('/settings/editarea?areaid='.$area_id)),
				array('label'=>'Measures', 'url'=>array('/settings/listofmeasures?areaid='.$area_id))
			),
		)); ?>
	</div>
</div>