<?php switch($page):?>
<?php case 1:?>
	<?php echo CHtml::beginForm(array('datacapture/capture?page=2'), 'post'); ?>
	<div class="label" style="float: none; clear:right;">Choose an Area:</div>
	<div class="field" style="float: none; clear:right;">
		<?php 
			if(count($area_array) != 0)
			{
				echo CHtml::dropDownList('areaid', Yii::app()->session['areaid'], $area_array); 
			}
			else
			{
				echo 'No Areas Found!';
			}
		?>
	</div>

	<div id="submit">
		<?php 
			if(count($area_array) != 0)
			{
				echo CHtml::submitButton('Next >>'); 
			}
		?>
	</div>
	<?php echo CHtml::endForm(); ?>
<?php break; ?>

<?php case 2:?>
	<?php echo CHtml::beginForm(array('datacapture/capture?page=3'), 'post'); ?>
	<div class="label" style="float: none; clear:right;">Choose a Measure:</div>
	<div class="field" style="float: none; clear:right;">
		<?php 
			if(count($measure_array) != 0)
			{
				echo CHtml::dropDownList('measureid', Yii::app()->session['measureid'], $measure_array); 
			}
			else
			{
				echo 'No Measures Found!';
			}
		?>
	</div>
	
	<div id="row" style="margin-top: 20px;">
		<?php echo CHtml::button('<< Previous', array('onClick'=>"window.location.href='capture?page=1'")); ?>
		<?php 
			if(count($measure_array) != 0)
			{
				echo CHtml::submitButton('Next >>'); 
			}
		?>
	</div>
	<?php echo CHtml::endForm(); ?>
<?php break; ?>

<?php case 3:?>
	<?php echo CHtml::beginForm(array('datacapture/capture?page=4'), 'post'); ?>
		<div class="label" style="float: none; clear:right;">Number of Entries:</div>
		<div class="field" style="float: none; clear:right;">
			<?php echo CHtml::numberField('numberofentries', '1', array('min'=>1)); ?>
		</div>
		
		<div id="row" style="margin-top: 20px;">
			<?php echo CHtml::button('<< Previous', array('onClick'=>"window.location.href='capture?page=2'")); ?>
			<?php echo CHtml::submitButton('Next >>'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
<?php break; ?>

<?php case 4:?>
	<?php echo '<table>'; ?>
	<?php
		foreach($rows as $row)
		{
			echo '<th style="text-align: center;">'.$row->row_name.'</th>';
		}
		foreach($columns as $column)
		{
			echo '<th style="text-align: center;">'.$column->column_name.'</th>';
		}
	?>
	
	<?php echo CHtml::beginForm(array('datacapture/capture?page=4'), 'post'); ?>
		<?php $j =0 ?>
		<?php for($i=0; $i<Yii::app()->session['numberofentries']; $i++):?>
			<?php echo '<tr>'; ?>
			<?php
				foreach($rows as $row)
				{
					echo '<td>';
					echo CHtml::activeHiddenField($model[$j], "[$j]field_name", array('value'=>$row->row_name));
					echo CHtml::activeHiddenField($model[$j], "[$j]data_type", array('value'=>$row->row_data_type));
					echo CHtml::activeTextField($model[$j], "[$j]field");
					echo '<span class="span-error" style="color:red;">'.CHtml::error($model[$j], "[$j]field").'</span>';
					echo '</td>';
					$j++;
				}
				foreach($columns as $column)
				{
					echo '<td>';
					echo CHtml::activeHiddenField($model[$j], "[$j]field_name", array('value'=>$column->column_name));
					echo CHtml::activeHiddenField($model[$j], "[$j]data_type", array('value'=>$column->column_data_type));
					echo CHtml::activeTextField($model[$j], "[$j]field");
					echo '<span class="span-error" style="color:red;">'.CHtml::error($model[$j], "[$j]field").'</span>';
					echo '</td>';
					$j++;
				}
			?>
			<?php echo '</tr>'; ?>
		<?php endfor;?>
		
		<?php echo '</table>'; ?>
		
		<div id="row" style="margin-top: 20px;">
			<?php echo CHtml::button('<< Previous', array('onClick'=>"window.location.href='capture?page=3'")); ?>
			<?php echo CHtml::submitButton('Submit'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
<?php break; ?>
<?php endswitch; ?>