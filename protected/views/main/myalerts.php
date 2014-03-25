<script>
	function markalert(alerts)
	{
		alert(alerts.getAttribute('alertid'));
	}
	
	function markalert(alerts)
	{
		var form = document.forms.namedItem('markAlertForm');
		var action = form.getAttribute('action');
		var formData = new FormData(form);
		
		formData.append('isAjax', 1);
		formData.append('alertid', alerts.getAttribute('alertid'));
				
		if(window.XMLHttpRequest)
		{
			request = new XMLHttpRequest();
		}
		else if(window.ActiveXObject)
		{
			try {
				request = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch(exception) {
				try {
					request = new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch(exception) {
				}
			}
		}
		
		if(!request)
		{
			alert('Sorry! The Browser cannot create an XMLHttp instance!');
			return false;
		}
		
		request.open("POST", action, true);
		request.onreadystatechange = processMarkAlert;
		request.send(formData);
	}

	function processMarkAlert()
	{
		try {
			if(request.readyState === 4 && request.status === 200)
			{
				//alert(request.responseText);
				location.reload();
			}
		}
		catch(exception) {
			alert(exception);
		}
	}
</script>

<style>
	table#alert-tbl
	{
		margin-left: 10px;
		font-size: 12px;
	}
	img#status
	{
		height: 25px;
		width: 25px;
	}
</style>

<div id="overview-name">
	<?php
		$this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>array(
				'Main'=>array('main/servicearea'),
				'Alerts',
			),
			'homeLink'=>false,
		));
	?>
</div>

<div id="service-area-container">
	<table id="alert-tbl" style="margin-right: auto;">
		<tr>
			<th style="width:70px;">Time/Date</th>
			<th>Description</th>
			<th style="width:50px;">Alert Code</th>
			<th style="text-align:center">Mark as Resolved</th>
		</tr>
		
		<?php
			if(count($alerts) > 0)
			{
				foreach($alerts as $alert)
				{
					echo '<tr>';
					echo '<td>'.$alert->date.'</td>';
					echo '<td>'.$alert->description.'</td>';
					$status = "";
					
					if($alert->alert_type == 'low threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-threat.png"';
					}
					else if($alert->alert_type == 'moderate threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-threat.png"';
					}
					else if($alert->alert_type == 'high threat level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-threat.png"';
					}
					else if($alert->alert_type == 'low opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/low-opportunity.png"';
					}
					else if($alert->alert_type == 'moderate opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/moderate-opportunity.png"';
					}
					else if($alert->alert_type == 'high opportunity level')
					{
						$status = '<img id="status" src="'.Yii::app()->request->baseUrl.'/images/high-opportunity.png"';
					}
					
					
					echo '<td>'.$status.'</td>';
					echo '<td style="text-align:center">';
						echo CHtml::beginForm(array('main/markalert/'), 'post', array('name'=>'markAlertForm'));
							echo CHtml::checkBox('', false, array('onchange'=>'markalert(this)', 'alertid'=>$alert->alert_id));
						echo CHtml::endForm();
					echo '</td>';
					echo '</tr>';
				}
			}
			else
			{
				echo '<tr>';
				echo '<td rowspan="3" style="padding-top: 10px;">NO ALERTS FOUND!</td>';
				echo '</tr>';
			}
		?>
	</table>
</div>