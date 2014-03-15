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
						'CREATE ROW HIERARCHY',
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
				   '$(".createhierarchy-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Row Hierarchy'),
					array('label'=>'Column Hierarchy', 'url'=>array('/settings/createcolumnhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				),
			)); ?>
			<?php if(Yii::app()->user->hasFlash('createhierarchy_success')):?>
				<div class="createhierarchy-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('createhierarchy_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('createhierarchy_failed')):?>
				<div class="createhierarchy-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('createhierarchy_failed'); ?>
				</div>
			<?php endif;?>
			</div>
			
			<div id="edit-data-area">	
				<div class="rowname-header">
					ROW DIMENSION
				</div>
				
				<div class="parent-header">
					PARENT ROW
				</div>
				<?php echo CHtml::beginForm(array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
					<?php foreach($model as $i=>$eachmodel): ?>
						<div class="hierarchy-label">
							<?php echo $row_name[$i];?>
							<?php echo CHtml::activeHiddenField($eachmodel, "[$i]category_id"); ?>
						</div>
						<div class="hierarchy-field" id="parent-hierarchy">
							<?php echo CHtml::activeDropDownList($eachmodel, "[$i]parent_id", $hierarchy_selection); ?>
						</div>
						<div class="hierarchy-error-msg">
							<?php echo CHtml::error($eachmodel, "[$i]parent_id"); ?>
						</div>
					<?php endforeach; ?>
					
					<div id="submit">
						<?php echo CHtml::submitButton('Create Hierarchy'); ?>
					</div>
				<?php echo CHtml::endForm(); ?>
			</div>
		</div>
	</div>
</div>