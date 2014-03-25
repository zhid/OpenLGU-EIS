<!--This Page is for the Settings Page-->
<style type="text/css">
	#controls ul li:nth-of-type(2) a {
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

<script type="text/javascript">
	function deleteYesClick(indicatorForm)
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		
		deletePrompt.style.display = "none";
		indicatorForm.submit();
	}

	function deleteNoClick()
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		deletePrompt.style.display = "none";
	}
	
	function showDeletePrompt(indicatorForm)
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		var deleteYes = document.getElementById("deleteYes");
		var deleteNo = document.getElementById("deleteNo");
		var nameContainer = document.getElementById("measure-del");
		
		deleteYes.addEventListener("click", function(){deleteYesClick(indicatorForm)}, false);
		deleteNo.addEventListener("click", deleteNoClick, false);
		deletePrompt.style.display = "block";
	
		return false;
	}
</script>

<div id="page-name">
	Settings
</div>

<div id="container">
	<div id="controls">
		<div id="control-name"><?php echo $measure->measure_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Add Dimension', 'url'=>array('/settings/addrowdimension?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Create Hierarchy', 'url'=>array('/settings/createrowhierarchy?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area->area_name).''=>array('settings/areaoverview?areaid='.$area->area_id),
						''.strtoupper($measure->measure_name).''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
						'LIST OF THRESHOLDS',
					),
					'homeLink'=>false,
				));
			?>
		</div>
		
		<div id="area-delete-prompt">
			<p>Would you like to delete this indicator?</p>
			<button id="deleteYes">YES</button>
			<button id="deleteNo">NO</button>
		</div>
		
		<div id="overview-data">
			<?php
				/*Fade out Effect for Success Message in Edit Area*/
				Yii::app()->clientScript->registerScript(
				   'myHideEffect',
				   '$(".addindicator-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
				   CClientScript::POS_READY
				);
			?>
			<div id="overview-data-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'List of Threholds'),
					array('label'=>'Add Threshold', 'url'=>array('/settings/addthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				),
			)); ?>
			
			</div>
			
			<?php if(Yii::app()->user->hasFlash('deletethreshold_success')):?>
				<div class="addindicator-flash-message" id="success-flash">
					<?php echo Yii::app()->user->getFlash('deletethreshold_success'); ?>
				</div>
			<?php endif;?>
			
			<?php if(Yii::app()->user->hasFlash('deletethreshold_failed')):?>
				<div class="addindicator-flash-message" id="failed-flash">
					<?php echo Yii::app()->user->getFlash('deletethreshold_failed'); ?>
				</div>
			<?php endif;?>
			
			<div id="edit-data-area">		
				<?php if($count > 4):?>
				<div id="pagination">
					<?php 
						$this->widget('CLinkPager', array(
							'pages' => $pages,
							'header' => '',
							'cssFile'=>false,
							'maxButtonCount'=>5,
							'firstPageLabel'=>'',
							'lastPageLabel'=>'',
						))
					?>
				</div>
				<?php endif;?>
				
				<div id="list-indicators">
					<table id="show-result">
					<th>Threshold</th>
					<th></th>
					
					<?php
						if($count > 0)
						{
							$i = 0;
							foreach($thresholds as $threshold)
							{
								echo '<tr>';
								if($threshold->highthreshold != NULL)
								{
									echo '<td>'.'If '.$column_name[$i++].' '.($threshold->lowthreshold_operator).' '.($threshold->lowthreshold).' AND '.($threshold->highthreshold_operator).' '.($threshold->highthreshold).' display a '.($threshold->threshold_type).'</td>';
								}
								else
								{	
									echo '<td>'.'If '.$column_name[$i++].' '.($threshold->lowthreshold_operator).' '.($threshold->lowthreshold).' display a '.($threshold->threshold_type).'</td>';
								}
								echo '<td>';
										echo CHtml::beginForm(array('settings/deletethreshold/'), 'post', array('thresholdid'=>$threshold->threshold_id, 'id'=>'deleteIndicatorForm', 'onsubmit'=>'return showDeletePrompt(this)'));
											echo CHtml::hiddenField('thresholdid', $threshold->threshold_id);
											echo CHtml::hiddenField('areaid', $area->area_id);
											echo CHtml::hiddenField('measureid', $measure->measure_id);
											
											echo CHtml::submitButton('delete', array('name'=>''));
										echo CHtml::endForm();
								echo '</td>';
								echo '</tr>';
							}
						}
						else
						{
							echo '
								<tr id="no-areas">
									<td colspan="2">NO THRESHOLD FOUND!</td>
								</tr>';
						}
					?>
				</table>
				</div>
			</div>
		</div>
	</div>
</div>