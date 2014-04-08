<style type="text/css">
	#mainmenu ul li:nth-of-type(3)
	{
		background-color: #eff4fa;
		padding-bottom: 10px;
		padding-top: 5px;
	}
	#mainmenu ul li:nth-of-type(3) a
	{
		color: #6399cd;
	}
</style>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/edituserinfo'),
					'Areas'=>array('settings/listofareas'),
					''.$area->area_name,
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="overview-data">	
		<div id="overview-data-area">	
			<div class="data-label">
				Area Name:
			</div>
			<div class="data-value">
				<?php echo $area->area_name?>
			</div>
			
			<div class="data-label">
				Service Area:
			</div>
			<div class="data-value">
				<?php 
					switch($area->service_area)
					{
						case 1:
							echo 'Administrative Governance';
							break;
						case 2:
							echo 'Social Governance';
							break;
						case 3:
							echo 'Economic Governance';
							break;
						case 4:
							echo 'Good Governance';
							break;
					}
				?>
			</div>
			
			<div class="data-label">
				Managing Office:
			</div>
			<div class="data-value">
				<?php echo $area->managing_office?>
			</div>
			
			<div class="data-label">
				Officer In Charge:
			</div>
			<div class="data-value">
				<?php echo $area->officer_in_charge?>
			</div>
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