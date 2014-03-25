<?php
	$this->pageTitle = Yii::app()->name . ' - Login';
	$this->breadcrumbs = array(
		
	);
?>

<style>
	div.row
	{
		height: auto;
		min-height: 46px;
		width: 910px;
	}
</style>

<div id="content">

	<h1>Login</h1>

	<p>Please fill out the following form with your login credentials:</p>

	<div class="form">
		<p class="note">Fields with <span class="required">*</span> are required.</p>
		
		<div id="login-error">
			<?php echo $this->loginError; ?>
		</div>

		<?php echo CHtml::beginForm(); ?>
			<div class="row">
				<?php echo CHtml::activeLabel($model, 'username'); ?>
				<?php echo CHtml::activeTextField($model, 'username'); ?>
				<?php echo CHtml::error($model, 'username'); ?>
			</div>
			
			<div class="row">
				<?php echo CHtml::activeLabel($model, 'password'); ?>
				<?php echo CHtml::activePasswordField($model, 'password');?>
				<?php echo CHtml::error($model, 'password'); ?>
			</div>
			
			<div class="row">
				<?php echo CHtml::submitButton('Login'); ?>
			</div>
		<?php echo CHtml::endForm(); ?>
	</div><!-- form -->
</div>