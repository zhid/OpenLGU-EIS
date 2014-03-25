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
<?php if(Yii::app()->user->hasFlash('addarea_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addarea_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('addarea_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('addarea_failed'); ?>
	</div>
<?php endif; ?>

<div id="add-form-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Add New Area',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="add-form">
		<?php echo CHtml::beginForm(array('settings/addnewarea'), 'post', array('enctype'=>'multipart/form-data')); ?>
			<div class="label">
				<?php echo CHtml::activeLabel($model, 'area_logo'); ?>
			</div>	
			<div class="field">
				<?php echo CHtml::activeDropDownList($model, 'area_logo', array('fa fa-home fa-5x'=>'Home', 'fa fa-money fa-5x'=>'Money', 'fa fa-building-o fa-5x'=>'Building', 'fa fa-bar-chart-o fa-5x'=>'Bar Chart', 'fa fa-pencil-square-o fa-5x'=>'Pencil',
				'fa fa-road fa-5x'=>'Road', 'fa fa-shopping-cart fa-5x'=>'Cart', 'fa fa-medkit fa-5x'=>'Med Kit', 'fa fa-leaf fa-5x'=>'Leaf', 'fa fa-wheelchair fa-5x'=>'Wheel Chair', 'fa fa-users fa-5x'=>'User', 'fa fa-phone fa-5x'=>'Phone',
				'fa fa-legal fa-5x'=>'Legal', 'fa fa-cogs fa-5x'=>'Cogs', 'fa fa-tasks fa-5x'=>'Task', 'fa fa-book fa-5x'=>'Book', 'fa fa-thumbs-up fa-5x'=>'Thumbs Up', 'fa fa-flag fa-5x'=>'Flag', 'fa fa-archive fa-5x'=>'Archive'), array('onchange'=>'showlogo(this)')); ?>
			</div>
			<div class="error-msg">
				<i id="area-logo" class="fa fa-home fa-5x" style="color: gray;"></i>
			</div>
		
			<div class="label">
				<?php echo CHtml::activeLabel($model, 'service_area'); ?>
			</div>	
			<div class="field">
				<?php echo CHtml::activeDropDownList($model, 'service_area', array('1'=>'Administrative Governance', '2'=>'Social Governance', '3'=>'Economic Governance', '4'=>'Good Governance', '5'=>'Environmental Governance')); ?>
			</div>
			<div class="error-msg">
				<!--Returns an error message if the areaname field is black-->
				<?php echo CHtml::error($model, 'service_area'); ?>
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

<div id="controls">
	<div class="portlet-decoration">Settings</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Areas', 'url'=>array('/settings/listofareas')),
				array('label'=>'Add New Area', 'url'=>array('/settings/addnewarea/'))
			),
		)); ?>
	</div>
</div>