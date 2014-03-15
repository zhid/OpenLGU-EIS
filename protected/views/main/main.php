<!--This page is the Main Panel for the Executive Information System-->
<style type="text/css">
	#main-menu ul li:nth-of-type(1) a {
		border-bottom: 5px solid #bfee32;
	}
</style>

<div id="app-name">
	Executive Information System
</div>
<?php
	/*foreach($areas as $area)
	{
		echo $area->area_name;
	}*/
?>
<div class="panel" style="height:<?php echo ($size/5)*300; ?>px">
	<?php
		foreach($areas as $area)
		{
			echo '
				<div class="wrapper">
					<div class="area-border">
						<div class="area-container">
							<img class="logo" src="'.(Yii::app()->request->baseUrl).'/images/logo/'.($area->area_logo).'" alt=""/>
						</div>
					</div>
					
					<div class="area-name">
						'.($area->area_name).'
					</div>
				</div>';
		}
	?>
</div>