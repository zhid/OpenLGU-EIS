<script>
	function clearSessionStorage()
	{
		if(window.sessionStorage)
		{	
			sessionStorage.clear();
		}
	}

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

<style>
	img#status
	{
		height: 18px;
		width: 18px;
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
				'Areas of Concern',
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="legend">
	<img id="color-rating" src="<?php echo Yii::app()->request->baseUrl; ?>/images/color-rating.png"/>
</div>

<div class="container" style="min-height: 450px;">
	<table style="margin-top: 20px;">
		<?php $i = 0; ?>
		<tbody>
			<?php foreach($areas as $area): ?>
			<?php 
				if((($i % 6) == 1 && $i != 1) || $i == 0)
				{
					echo '<tr>';
				}
			?>
			<?php
				if($area->color_rating == 1){ $color = '#1a9954';}
				else if($area->color_rating == 2){ $color = '#7df144';}
				else if($area->color_rating == 3){ $color = '#fafc05';}
				else if($area->color_rating == 4){ $color = '#ffc105';}
				else if($area->color_rating == 5){ $color = '#f50704';}
				else { $color = '#0066cc';}
			?>
				<td width="150" onclick="clearSessionStorage()"><a href="<?php echo Yii::app()->getHomeUrl().'/main/dashboard?servicearea='.$servicearea.'&areaid='.($area->area_id) ?> "><center><i class="<?php echo $area->area_logo; ?>" style="color:<?php echo $color;?>"></i></center><center><?php echo $area->area_name ?></center></a></td>
				<?php $i++; ?>
			<?php 
				if($i % 6 == 0 && $i != 0)
				{
					echo '</tr>';
				}
			?>
			<?php endforeach; ?>
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