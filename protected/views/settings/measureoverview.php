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
		<div id="control-name"><?php echo $measure->measure_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Indicators', 'url'=>array('/settings/listindicators?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
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
						''.strtoupper($measure->measure_name),
					),
					'homeLink'=>false,
				));
			?>
		</div>
		
		<div id="overview-data">
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'Overview'),
					array('label'=>'Edit', 'url'=>array('/settings/editmeasure?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				),
			)); ?>
			</div>
			
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
</div>