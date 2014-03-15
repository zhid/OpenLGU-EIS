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
					array('label'=>'Overview'),
					array('label'=>'Edit', 'url'=>array('/settings/editarea?areaid='.$area_id))
				),
			)); ?>
			</div>
			
			<div id="overview-data-area">		
				<div class="data-label">
					Area Name:
				</div>
				<div class="data-value">
					<?php echo $area->area_name?>
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
			
			<div id="area-data-logo">
				<img src="<?php echo Yii::app()->request->baseUrl;?>/images/logo/<?php echo $area->area_logo?>"/>
			</div>
		</div>
	</div>
</div>