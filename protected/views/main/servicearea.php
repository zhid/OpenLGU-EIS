<style>
	#service-area-container table td
	{
		height: 200px;
		width: 300px;
	}
	img#status
	{
		height: 18px;
		width: 18px;
	}
</style>

<script>
	$(function(){
		jQuery('#webticker').webTicker();
		
		$("selector").webTicker({
			speed: 50, //pixels per second
			direction: "left", //if to move left or right
			moving: true, //weather to start the ticker in a moving or static position
			startEmpty: true, //weather to start with an empty or pre-filled ticker
			duplicate: false, //if there is less items then visible on the ticker you can duplicate the items to make it continuous
			rssurl: false, //only set if you want to get data from rss
			rssfrequency: 0, //the frequency of updates in minutes. 0 means do not refresh
			updatetype: "reset" //how the update would occur options are "reset" or "swap"
		}); 
	});
</script>

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
				'Service Areas',
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="legend">
	<img id="color-rating" src="<?php echo Yii::app()->request->baseUrl; ?>/images/color-rating.png"/>
</div>

<div id="service-area-container">
	<table>
		<tbody>
			<tr>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=1"><center><i class="fa fa-gavel fa-5x" style="color:<?php echo $colors[1]?>"></i></center><center>Administrative Governance</center></a></td>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=2"><center><i class="fa fa-users fa-5x" style="color:<?php echo $colors[2]?>"></i></center><center>Social Governance</center></a></td>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=3"><center><i class="fa fa-money fa-5x" style="color:<?php echo $colors[3]?>"></i></center><center>Economic Governance</center></a></td>
			</tr>
			
			<tr>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=4"><center><i class="fa fa-check-square-o fa-5x" style="color:<?php echo $colors[4]?>"></i></center><center>Good Governance</center></a></td>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=5"><center><i class="fa fa-leaf fa-5x" style="color:<?php echo $colors[5]?>"></i></center><center>Environmental Governance</center></a></td>
			</tr>
		</tbody>
	</table> 
</div>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/webticker.js',CClientScript::POS_HEAD); ?>
<?php
	echo '<ul id="webticker">';
	
	foreach($alerts as $alert)
	{
		$status = "";			
		if($alert->alert_type == 'low threat level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-threat.png" />';
		}
		else if($alert->alert_type == 'moderate threat level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-threat.png" />';
		}
		else if($alert->alert_type == 'high threat level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-threat.png" />';
		}
		else if($alert->alert_type == 'low opportunity level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-opportunity.png" />';
		}
		else if($alert->alert_type == 'moderate opportunity level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-opportunity.png" />';
		}
		else if($alert->alert_type == 'high opportunity level')
		{
			$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-opportunity.png" />';
		}
	
		echo '<li><a href="'.Yii::app()->getHomeUrl().'/main/myalerts?servicearea">'.$status.'  '.'('.$alert->date.') '.$alert->description.'</a></li>';
	}
	echo '</ul>';
?>