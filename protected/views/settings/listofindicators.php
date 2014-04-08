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

<?php
	Yii::app()->clientScript->registerScript(
	   'myHideEffect',
	   '$("#flash-msg").animate({opacity: 1.0}, 3000).fadeOut("slow");',
	   CClientScript::POS_READY
	);
?>
<?php if(Yii::app()->user->hasFlash('deletethreshold_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletethreshold_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('deletethreshold_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletethreshold_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/edituserinfo'),
					'Areas'=>array('settings/listofareas'),
					''.$area->area_name.''=>array('settings/areaoverview?areaid='.$area->area_id),
					''.$measure->measure_name.''=>array('/settings/measureoverview?areaid='.($area->area_id).'&measureid='.($measure->measure_id)),
					'List of Thresholds',
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
			<th>Thresholds</th>
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
									echo CHtml::hiddenField('columnid', $threshold->column_id);
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

<div id="controls">
	<div class="portlet-decoration">Thresholds</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Thresholds', 'url'=>array('/settings/listthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id))),
				array('label'=>'Add Threshold', 'url'=>array('/settings/addthresholds?areaid='.($area->area_id).'&measureid='.($measure->measure_id)))
			),
		)); ?>
	</div>
</div>