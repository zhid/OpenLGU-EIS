<!--This Page is for the Settings Page-->
<style type="text/css">
	#controls ul li:nth-of-type(3) a {
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
				array('label'=>'Add New Measure', 'url'=>array('/settings/addnewmeasure?areaid='.$area_id.'&page='.$page))
			),
		)); ?>
	</div>
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area->area_name).''=>array('/settings/areaoverview?areaid='.$area_id),
						'ADD NEW MEASURE',
					),
					'homeLink'=>false,
				));
			?>
		</div>
		<?php
			/*Fade out Effect for Success Message in Add New Area*/
			Yii::app()->clientScript->registerScript(
			   'myHideEffect',
			   '$(".addmeasure-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
			   CClientScript::POS_READY
			);
		?>
		<div id="addmeasure-flash-message-container">
			<div id="page-number-indicator">
				<?php echo 'Page '.$page.' of 3';?>
			</div>
			
			<?php if(Yii::app()->user->hasFlash('addmeasure_success')):?>
				<div class="addmeasure-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('addmeasure_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('addmeasure_failed')):?>
				<div class="addmeasure-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('addmeasure_failed'); ?>
				</div>
			<?php endif;?>
		</div>
		
		<div id="add-measure-form">
			<?php 
				$this->renderPartial('addnewmeasureform', array('area'=>$area, 'area_id'=>$area_id, 'page'=>$page,'model'=>$model));
			?>
		</div>
	</div>
</div>