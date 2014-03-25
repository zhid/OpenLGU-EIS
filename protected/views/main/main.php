<script>
function clearSessionStorage()
{
	if(window.sessionStorage)
	{	
		sessionStorage.clear();
	}
}
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
				'Main'=>array('main/servicearea'),
				'Areas of Concern',
			),
			'homeLink'=>false,
		));
	?>
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