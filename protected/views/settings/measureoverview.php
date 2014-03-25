<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/listofareas'),
					''.$area->area_name.''=>array('settings/areaoverview?areaid='.$area->area_id),
					''.$measure->measure_name,
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="overview-data">	
		<div id="overview-data-area">		
			<div class="data-label">
				Measure Name:
			</div>
			<div class="data-value">
				<?php echo $measure->measure_name; ?>
			</div>
			
			<div class="data-label">
				Description:
			</div>
			<div class="data-value">
				<?php echo $measure->description; ?>
			</div>
		</div>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration"><?php echo $measure->measure_name; ?></div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Edit Measure', 'url'=>array('/settings/editmeasure?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Dimensions', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Hierarchies', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
</div>