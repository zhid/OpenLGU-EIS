<script type="text/javascript">
	function deleteYesClick(measureForm)
	{
		var deleteForm = document.getElementById('deleteMeasureForm');
		
		measureForm.submit();
		deletePrompt.style.display = "none";
	}

	function deleteNoClick()
	{
		var deletePrompt = document.getElementById('measure-delete-prompt');
		deletePrompt.style.display = "none";
	}
	
	function showDeletePrompt(measureName, measureForm)
	{
		var deletePrompt = document.getElementById('measure-delete-prompt');
		var deleteYes = document.getElementById("deleteYes");
		var deleteNo = document.getElementById("deleteNo");
		var nameContainer = document.getElementById("measure-del");
		
		nameContainer.innerHTML = measureForm.getAttribute('measurename');
		deleteYes.addEventListener("click", function(){deleteYesClick(measureForm)}, false);
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
<?php if(Yii::app()->user->hasFlash('deletemeasure_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletemeasure_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('deletemeasure_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletemeasure_failed'); ?>
	</div>
<?php endif; ?>

<div id="overview-container">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'Settings'=>array('settings/edituserinfo'),
					'Areas'=>array('settings/listofareas'),
					''.$area_name.''=>array('/settings/areaoverview?areaid='.$area_id),
					'List of Measures',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="measure-delete-prompt">
		<p>Would you like to delete <span id="measure-del"></span>?</p>
		<button id="deleteYes">YES</button>
		<button id="deleteNo">NO</button>
	</div>
	
	<div id="lists">
		<div id="lists-container">
			<div id="search-form">
				<?php echo CHtml::beginForm(array('settings/searchmeasure/'), 'get'); ?>
					<?php echo CHtml::hiddenField('areaid', $area_id); ?>
					<?php echo CHtml::textField('keyword', $keyword, array('placeholder'=>'measure name')); ?>
					<?php echo CHtml::submitButton('search', array('name'=>'')); ?>
				<?php echo CHtml::endForm(); ?>
			</div>
			
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
			
			<div id="measure-search-result">
				<table id="measure-show-result">
					<th>Measure Name</th>
					<th>Alert Level</th>
					<th></th>
					<th></th>
					
					<?php
						$i = 0;
						if($count > 0)
						{
							foreach($measures as $measure)
							{
								echo '
								<tr>
									<td>'.($measure->measure_name).'</td>
									<td>'.($measure->alert_level).'</td>
									<td>';
										echo CHtml::beginForm(array('settings/measureoverview/'), 'get');
											echo CHtml::hiddenField('areaid', $area_id);
											echo CHtml::hiddenField('measureid', $measure->measure_id);
											echo CHtml::submitButton('view', array('name'=>''));
										echo CHtml::endForm();
									
								echo '</td>';
								echo '<td>';
										echo CHtml::beginForm(array('settings/deletemeasure/'), 'post', array('measurename'=>$measure->measure_name, 'id'=>'deleteMeasureForm', 'onsubmit'=>'return showDeletePrompt("'.($measure->measure_name).'", this)'));
											echo CHtml::hiddenField('areaid', $area_id);
											echo CHtml::hiddenField('measureid', $measure->measure_id);
											echo CHtml::submitButton('delete', array('name'=>''));
										echo CHtml::endForm();
								echo '</td>
								</tr>';
							}
						}
						else
						{
							echo '
								<tr id="no-areas">
									<td colspan="6">NO MEASURES FOUND!</td>
								</tr>';
						}
					?>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration">Measures</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Measures', 'url'=>array('/settings/listofmeasures?areaid='.$area_id)),
				array('label'=>'Add New Measure', 'url'=>array('/settings/addnewmeasure?areaid='.$area_id.'&page=1'))
			),
		)); ?>
	</div>
</div>