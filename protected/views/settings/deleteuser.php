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
		
		nameContainer.innerHTML = areaForm.getAttribute('username');
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
<?php if(Yii::app()->user->hasFlash('deleteuser_success')):?>
	<div class="flash-success" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deleteuser_success'); ?>
	</div>
<?php endif; ?>
<?php if(Yii::app()->user->hasFlash('deleteuser_failed')):?>
	<div class="flash-error" id="flash-msg">
		<?php echo Yii::app()->user->getFlash('deleteuser_failed'); ?>
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
					'Settings'=>array('settings/edituserinfo'),
					'Delete User',
				),
				'homeLink'=>false,
			));
		?>
	</div>
	
	<div id="lists-container">	
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
				<th>Username</th>
				<th>Managing Area</th>
				<th></th>
				
				<?php
					$i = 0;
					if($count > 0)
					{
						foreach($users as $user)
						{
							echo '
							<tr>
								<td>'.($user->username).'</td>
								<td>'.$area_array[$i++].'</td>';
							echo '<td>';
									echo CHtml::beginForm(array('settings/deleteuser/'), 'post', array('username'=>$user->username, 'id'=>'deleteAreaForm', 'onsubmit'=>'return showDeletePrompt("'.($user->username).'", this)'));
										echo CHtml::hiddenField('username', $user->username);
								
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
								<td colspan="5">NO USERS FOUND!</td>
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
				array('label'=>'Add Users', 'url'=>array('/settings/adduser')),
				array('label'=>'Delete Users', 'url'=>array('/settings/deleteuser')),
				array('label'=>'Areas', 'url'=>array('/settings/listofareas'))
			),
		)); ?>
	</div>
</div>