<style>
	#service-area-container table td
	{
		height: 200px;
		width: 300px;
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
				'Service Areas',
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="service-area-container">
	<table>
		<tbody>
			<tr>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=1"><center><i class="fa fa-gavel fa-5x"></i></center><center>Administrative Governance</center></a></td>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=2"><center><i class="fa fa-users fa-5x"></i></center><center>Social Governance</center></a></td>
			</tr>
			
			<tr>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=3"><center><i class="fa fa-money fa-5x"></i></center><center>Economic Governance</center></a></td>
				<td width="150"><a href="<?php echo Yii::app()->getHomeUrl();?>/main/panel?servicearea=4"><center><i class="fa fa-check-square-o fa-5x"></i></center><center>Good Governance</center></a></td>
			</tr>
		</tbody>
	</table> 
</div>