<style>
	table#alert-tbl
	{
		margin-left: 10px;
	}
	img#status
	{
		height: 30px;
		width: 30px;
	}
</style>

<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>

<?php if(Yii::app()->user->hasFlash('main-flash')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('main-flash'); ?>
	</div>
<?php endif;?>

<div id="overview-name">
	<?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>array(
				'Main'=>array('main/servicearea'),
				'Areas of Concern'=>array('/main/panel?servicearea='.$servicearea),
				'Dashboard'=>array('/main/dashboard?servicearea='.$servicearea.'&areaid='.$areaid),
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="service-area-container">
	<table id="alert-tbl" style="margin-right: auto;">
		<tr>
			<th>Date</th>
			<th>Description</th>
			<th>Status</th>
		</tr>
		
		<?php
			if(count($alerts) > 0)
			{
				foreach($alerts as $alert)
				{
					echo '<tr>';
					echo '<td>'.$alert->date.'</td>';
					echo '<td>'.$alert->description.'</td>';
					$status = "";
					
					if($alert->alert_type == 'low threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-threat.png"';
					}
					else if($alert->alert_type == 'moderate threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-threat.png"';
					}
					else if($alert->alert_type == 'high threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-threat.png"';
					}
					else if($alert->alert_type == 'low opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-opportunity.png"';
					}
					else if($alert->alert_type == 'moderate opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-opportunity.png"';
					}
					else if($alert->alert_type == 'high opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-opportunity.png"';
					}
					
					
					echo '<td>'.$status.'</td>';
					echo '</tr>';
				}
			}
			else
			{
				echo '<tr>';
				echo '<td rowspan="3" style="padding-top: 10px;">NO ALERTS FOUND!</td>';
				echo '</tr>';
			}
		?>
	</table>
</div>