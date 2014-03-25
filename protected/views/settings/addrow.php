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
		<div id="control-name"><?php echo $measure->measure_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Add Dimension', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Create Hierarchy', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area->area_name).''=>array('settings/areaoverview?areaid='.$area->area_id),
						''.strtoupper($measure->measure_name).''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
						'ADD ROW',
					),
					'homeLink'=>false,
				));
			?>
		</div>
		
		<div id="overview-data">
			<?php
				/*Fade out Effect for Success Message in Edit Area*/
				Yii::app()->clientScript->registerScript(
				   'myHideEffect',
				   '$(".addrow-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Add Row'),
					array('label'=>'Add Column', 'url'=>array('/settings/addcolumndimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				),
			)); ?>
			<?php if(Yii::app()->user->hasFlash('addrow_success')):?>
				<div class="addrow-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('addrow_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('addrow_failed')):?>
				<div class="addrow-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('addrow_failed'); ?>
				</div>
			<?php endif;?>
			</div>
			
			<div id="edit-data-area">		
				<?php echo CHtml::beginForm(array('settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
					<?php echo CHtml::activeHiddenField($model, 'measure_id', array('value'=>$measure->measure_id)); ?>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'row_name'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeTextField($model, 'row_name'); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'row_name'); ?>
					</div>
					
					<div class="label">
						<?php echo CHtml::activeLabel($model, 'row_data_type'); ?>
					</div>
					<div class="field">
						<?php echo CHtml::activeDropDownList($model, 'row_data_type', array('bigint'=>'integer', 'double precision'=>'float', 'text'=>'text')); ?>
					</div>
					<div class="error-msg">
						<?php echo CHtml::error($model, 'row_data_type'); ?>
					</div>
					
					<div id="submit">
						<?php echo CHtml::submitButton('Add Row'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>