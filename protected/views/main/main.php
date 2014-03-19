<!--This page is the Main Panel for the Executive Information System-->
<style type="text/css">
	#main-menu ul li:nth-of-type(1) a {
		border-bottom: 5px solid #bfee32;
	}
</style>

<script>
function openArea()
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
	   '$("#main-flash").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>

<?php if(Yii::app()->user->hasFlash('main-flash')):?>
	<div id="main-flash">
		<?php echo Yii::app()->user->getFlash('main-flash'); ?>
	</div>
<?php endif;?>

<div id="app-name">
	Executive Information System
</div>
<div class="panel" style="height:<?php echo ($size/5)*300; ?>px">
	<?php
		foreach($areas as $area)
		{
			echo '
				<div class="wrapper">
					<a onclick="openArea()" href="'.Yii::app()->getHomeUrl().'/main/dashboard?areaid='.($area->area_id).'">
					<div class="area-border">
						<div class="area-container">
							<div class="color-rating-container">
								<img style="-webkit-transform: rotate(0deg); -moz-transform: rotate(0deg);" class="pin" src="'.(Yii::app()->request->baseUrl).'/images/meter-pin.png" />
							</div>
							<img class="area-logo" src="'.(Yii::app()->request->baseUrl).'/images/logo/'.($area->area_logo).'" alt="'.($area->area_logo).'" />
						</div>
					</div>
					</a>
					<div class="area-name">
						'.($area->area_name).'
					</div>
				</div>';
		}
	?>
</div>