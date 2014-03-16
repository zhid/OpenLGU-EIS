<?php /* this a layout used for the login page of EIS */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="language" content="en" />
		
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/login.css" />

		<title>Log In</title>
	</head>

	<body>
		<div id="banner">
		</div>
		
		<img id="eis-logo" src="<?php echo Yii::app()->request->baseUrl; ?>/images/EISlogo.png" alt="EIS" />
	
		<?php echo $content; ?>
	
		<div id="footer">
			OpenLGU: Executive Information System version 1.0
		</div>
	</body>
</html>
