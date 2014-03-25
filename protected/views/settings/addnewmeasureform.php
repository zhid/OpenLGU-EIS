<?php switch($page):?>
<?php case 1:?>
<div class="add-measure">
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page='.($page)), 'post'); ?>	
		<div class="label">
			<?php echo CHtml::activeLabel($model, 'measure_name'); ?>
		</div>
		<div class="field">
			<?php echo CHtml::activeTextField($model, 'measure_name', array('value'=>Yii::app()->session['measure_name'])); ?>
		</div>
		<div class="error-msg">
			<?php echo CHtml::error($model, 'measure_name'); ?>
		</div>
		
		<div class="label">
			<?php echo CHtml::activeLabel($model, 'number_of_rows'); ?>
		</div>
		<div class="field">
			<?php echo CHtml::activeNumberField($model, 'number_of_rows', array('min'=>1, 'value'=>Yii::app()->session['number_of_rows'])); ?>
		</div>
		<div class="error-msg">
			<?php echo CHtml::error($model, 'number_of_rows'); ?>
		</div>
		
		<div class="label">
			<?php echo CHtml::activeLabel($model, 'number_of_columns'); ?>
		</div>
		<div class="field">
			<?php echo CHtml::activeNumberField($model, 'number_of_columns', array('min'=>1, 'value'=>Yii::app()->session['number_of_columns'])); ?>
		</div>
		<div class="error-msg">
			<?php echo CHtml::error($model, 'number_of_columns'); ?>
		</div>
		
		<div class="label">
			<?php echo CHtml::activeLabel($model, 'description'); ?>
		</div>
		<div class="text-area-field ">
			<?php echo CHtml::activeTextArea($model, 'description', array('value'=>Yii::app()->session['description'])); ?>
		</div>
		<div class="text-area-error-msg">
			<?php echo CHtml::error($model, 'description'); ?>
		</div>
		
		<div id="submit">
			<?php echo CHtml::submitButton('Next >>'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
	
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page=4'), 'post'); ?>
		<div id="submit">
			<?php echo CHtml::hiddenField('clear', 'clear'); ?>
			<?php echo CHtml::submitButton('Clear'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>
<?php break; ?>

<?php case 2:?>
<div class="add-measure">
	<div class="row-header">
		Row Name
	</div>
	
	<div class="row-header">
		Row DataType
	</div>
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page='.($page)), 'post'); ?>
		<?php foreach($model as $i=>$eachmodel):?>
			<div class="row">
				<div class="row-field">
					<?php $name_index = 'row'.$i.'_name';?>
					<?php echo CHtml::activeTextField($eachmodel, "[$i]row_name", array('value'=>Yii::app()->session[$name_index])); ?>
				</div>
				<div class="row-error-msg">
					<?php echo CHtml::error($eachmodel, "[$i]row_name"); ?>
				</div>
			</div>
			
			<div class="row">
				<div class="row-field">
					<?php $type_index = 'row'.$i.'_data_type'; $val = Yii::app()->session[$type_index];?>
					<?php echo CHtml::activeDropDownList($eachmodel, "[$i]row_data_type", array('bigint'=>'integer', 'double precision'=>'float', 'text'=>'text'), array('options'=>array($val=>array('selected'=>true)))); ?>
				</div>
				<div class="row-error-msg">
					<?php echo CHtml::error($eachmodel, '[$i]row_data_type'); ?>
				</div>
			</div>
		<?php endforeach;?>
		<div class="row">
			<?php echo CHtml::button('<< Previous', array('onClick'=>"window.location.href='addnewmeasure?areaid=".($area_id)."&page=".($page-1)."'")); ?>
			<?php echo CHtml::submitButton('Next >>'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
	
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page=4'), 'post'); ?>
		<div class="row">
			<?php echo CHtml::hiddenField('clear', 'clear'); ?>
			<?php echo CHtml::submitButton('Clear'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>
<?php break; ?>

<?php case 3:?>
<div class="add-measure">
	<div class="row-header">
		Column Name
	</div>
	
	<div class="row-header">
		Column DataType
	</div>
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page='.($page)), 'post'); ?>
		<?php foreach($model as $i=>$eachmodel):?>
			<div class="row">
				<div class="row-field">
					<?php $name_index = 'column'.$i.'_name';?>
					<?php echo CHtml::activeTextField($eachmodel, "[$i]column_name", array('value'=>Yii::app()->session[$name_index])); ?>
				</div>
				<div class="row-error-msg">
					<?php echo CHtml::error($eachmodel, "[$i]column_name"); ?>
				</div>
			</div>
			
			<div class="row">
				<div class="row-field">
					<?php $type_index = 'column'.$i.'_data_type'; $val = Yii::app()->session[$type_index];?>
					<?php echo CHtml::activeDropDownList($eachmodel, "[$i]column_data_type", array('bigint'=>'integer', 'double precision'=>'float', 'text'=>'text'), array('options'=>array($val=>array('selected'=>true)))); ?>
				</div>
				<div class="row-error-msg">
					<?php echo CHtml::error($eachmodel, '[$i]column_data_type'); ?>
				</div>
			</div>
		<?php endforeach;?>
		<div class="row">
			<?php echo CHtml::button('<< Previous', array('onClick'=>"window.location.href='addnewmeasure?areaid=".($area_id)."&page=".($page-1)."'")); ?>
			<?php echo CHtml::submitButton('Add Measure'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
	
	<?php echo CHtml::beginForm(array('settings/addnewmeasure?areaid='.($area_id).'&page=4'), 'post'); ?>
		<div class="row">
			<?php echo CHtml::hiddenField('clear', 'clear'); ?>
			<?php echo CHtml::submitButton('Clear'); ?>
		</div>
	<?php echo CHtml::endForm(); ?>
</div>
<?php break; ?>

<?php endswitch;?>