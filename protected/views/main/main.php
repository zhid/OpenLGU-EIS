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
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl().'/main/dashboard?servicearea='.$servicearea.'&areaid='.($area->area_id) ?> "><center><i class="<?php echo $area->area_logo; ?>"></i></center><center><?php echo $area->area_name ?></center></a></td>
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