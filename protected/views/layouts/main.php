<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/settings.css" />
	
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
	<body>
		<div id="main-menu-container">
			<div id="user-note">
				Welcome, <?php echo Yii::app()->user->name; ?>
			</div>
		
			<div id="main-menu">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label'=>'Main', 'url'=>array('/main/')),
						array('label'=>'Help', 'url'=>array('/main/help')),
						array('label'=>'Settings', 'url'=>array('/settings/listofareas')),
						array('label'=>'Logout', 'url'=>array('/main/logout'), 'visible'=>!Yii::app()->user->isGuest)
					),
				)); ?>
			</div>
		</div>
		
		<img id="eis-logo" src="<?php echo Yii::app()->request->baseUrl; ?>/images/EISlogo.png" alt="EIS" />
		<?php echo $content; ?>

		<div id="footer">
			OpenLGU: Executive Information System version 1.0
		</div>
	</body>
</html>
