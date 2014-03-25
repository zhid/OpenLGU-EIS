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
	div.ui-dialog-titlebar {
		font-size : 10px;
	}
	div.ui-dialog-content {
		font-size : 15px;
		font-family : Arial;
		background-color: green;
	}
</style>

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

<div id="page-name">
	Settings
</div>

<div id="container">
	<div id="controls">
		<div id="control-name"><?php echo $area_name; ?></div>
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'Overview', 'url'=>array('/settings/areaoverview?areaid='.$area_id)),
				array('label'=>'List of Measures', 'url'=>array('/settings/listofmeasures?areaid='.$area_id)),
				array('label'=>'Add New Measure', 'url'=>array('/settings/addnewmeasure?areaid='.$area_id.'&page=1'))
			),
		)); ?>
	</div>
	
	
	<div id="overview-container">
		<div id="overview-name">
			<?php
				$this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>array(
						'SETTINGS'=>array('settings/listofareas'),
						''.strtoupper($area_name).''=>array('/settings/areaoverview?areaid='.$area_id),
						'LIST OF MEASURES',
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
				<?php
					Yii::app()->clientScript->registerScript(
					   'myHideEffect',
					   '$(".deletemeasure-flash-message").animate({opacity: 1.0}, 3000).fadeOut("slow");',
					   CClientScript::POS_READY
					);
				?>
				<div id="search-form">
					<?php echo CHtml::beginForm(array('settings/searchmeasure/'), 'get'); ?>
						<?php echo CHtml::hiddenField('areaid', $area_id); ?>
						<?php echo CHtml::textField('keyword', $keyword); ?>
						<?php echo CHtml::submitButton('search', array('name'=>'')); ?>
					<?php echo CHtml::endForm(); ?>
					
					<?php if(Yii::app()->user->hasFlash('deletemeasure_success')):?>
						<div class="deletemeasure-flash-message" id="success-flash">
							<?php echo Yii::app()->user->getFlash('deletemeasure_success'); ?>
						</div>
					<?php endif;?>
					
					<?php if(Yii::app()->user->hasFlash('deletemeasure_failed')):?>
						<div class="deletemeasure-flash-message" id="failed-flash">
							<?php echo Yii::app()->user->getFlash('deletemeasure_failed'); ?>
						</div>
					<?php endif;?>
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
						<th>View Measure</th>
						<th>Delete Measure</th>
						
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
</div>