<!--This Page is for the Settings Page-->
<script type="text/javascript">
	function deleteYesClick(areaForm)
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		
		deletePrompt.style.display = "none";
		areaForm.submit();
	}

	function deleteNoClick()
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		deletePrompt.style.display = "none";
	}
	
	function showDeletePrompt(areaName, areaForm)
	{
		var deletePrompt = document.getElementById('area-delete-prompt');
		var deleteYes = document.getElementById("deleteYes");
		var deleteNo = document.getElementById("deleteNo");
		var nameContainer = document.getElementById("measure-del");
		
		nameContainer.innerHTML = areaForm.getAttribute('areaname');
		deleteYes.addEventListener("click", function(){deleteYesClick(areaForm)}, false);
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
<?php if(Yii::app()->user->hasFlash('deletearea_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletearea_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('deletearea_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deletearea_failed'); ?>
	</div>
<?php endif; ?>

<div id="area-delete-prompt">
	<p>Would you like to delete <span id="measure-del"></span>?</p>
	<button id="deleteYes">YES</button>
	<button id="deleteNo">NO</button>
</div>
	
<div id="lists">
	<div id="overview-name">
		<?php
			$this->widget('zii.widgets.CBreadcrumbs', array(
				'links'=>array(
					'List of Areas',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="lists-container">
		<div id="search-form">
			<?php echo CHtml::beginForm(array('settings/searcharea/'), 'get'); ?>
				<?php echo CHtml::textField('keyword', $keyword, array('placeholder'=>'area name')); ?>
			
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
		
		<div id="search-result">
			<table id="show-result">
				<th>Area Name</th>
				<th>Managing Office</th>
				<th>Officer In Charge</th>
				<th></th>
				<th></th>
				
				<?php
					if($count > 0)
					{
						foreach($areas as $area)
						{
							echo '
							<tr>
								<td>'.($area->area_name).'</td>
								<td>'.($area->managing_office).'</td>
								<td>'.($area->officer_in_charge).'</td>
								<td>';
									echo CHtml::beginForm(array('settings/areaoverview/'), 'get');
										echo CHtml::hiddenField('areaid', $area->area_id);
								
										echo CHtml::submitButton('view', array('name'=>''));
									echo CHtml::endForm();
								
							echo '</td>';
							echo '<td>';
									echo CHtml::beginForm(array('settings/deletearea/'), 'post', array('areaname'=>$area->area_name, 'id'=>'deleteAreaForm', 'onsubmit'=>'return showDeletePrompt("'.($area->area_name).'", this)'));
										echo CHtml::hiddenField('areaid', $area->area_id);
								
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
								<td colspan="5">NO AREAS FOUND!</td>
							</tr>';
					}
				?>
			</table>
		</div>
	</div>
</div>

<div id="controls">
	<div class="portlet-decoration">Settings</div>
	<div class="portlet-content">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'List of Areas', 'url'=>array('/settings/listofareas')),
				array('label'=>'Add New Area', 'url'=>array('/settings/addnewarea/'))
			),
		)); ?>
	</div>
</div>