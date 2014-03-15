<!--This page contains the login form for the Executive Information System-->
<div id="app-name">
	Executive Information System
</div>

<div id="login-background">
	<div id="login-border">	
		<div id="login-form">
			<div id="login-name">Login</div>
			
			<div id="login-error">
				<?php echo $this->loginError; ?>
			</div>
			
			<?php echo CHtml::beginForm(); ?>
				<div>
					<?php echo CHtml::activeLabel($model, 'username'); ?>
				</div>
				
				<div>
					<?php echo CHtml::activeTextField($model, 'username'); ?>
				</div>
				
				<div class="error-msg">
					<!--Returns an error message if the username field is black-->
					<?php echo CHtml::error($model, 'username'); ?>
				</div>
				
				<div>
					<?php echo CHtml::activeLabel($model, 'password'); ?>
				</div>
				
				<div class="row">
					<?php echo CHtml::activePasswordField($model, 'password');?>
				</div>
				
				<div class="error-msg">
					<!--Returns an error message if the password field is black-->
					<?php echo CHtml::error($model, 'password'); ?>
				</div>
				
				<div class="row">
					<?php echo CHtml::submitButton('Login'); ?>
				</div>
			<?php echo CHtml::endForm(); ?>
		</div>
	</div>
</div>