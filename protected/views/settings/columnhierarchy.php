<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('createhierarchy_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('createhierarchy_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('createhierarchy_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('createhierarchy_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/listofareas'),
					''.$area->area_name.''=>array('settings/areaoverview?areaid='.$area->area_id),
					''.$measure->measure_name.''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
					'Column Hierarchy',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="overview-data">	
		<div id="edit-data-area">	
			<div class="rowname-header">
				Column Dimension
			</div>
			
			<div class="parent-header">
				Parent Column
			</div>
			<?php echo CHtml::beginForm(array('/settings/createcolumnhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)), 'post'); ?>
				<?php foreach($model as $i=>$eachmodel): ?>
					<div class="hierarchy-label">
						<?php echo $column_name[$i];?>
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

<div id="controls">
	<div class="portlet-decoration">Hierarchies</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Row Hierarchy', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Column Hierarchy', 'url'=>array('/settings/createcolumnhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
</div>