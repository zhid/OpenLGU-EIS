<?php
/* @var $this SiteController */
/* @var $error array */

$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<?php
	switch($code)
	{
		case 404:
			$message = "Sorry, the page you have requested can't be found. 
				Either the URL of your requested page is incorrect 
				or the page has been removed or moved to a new URL 
				.We apologise for any inconvenience caused.";
			break;
		case 500:
			$message = "The server encountered an internal error or misconfiguration
						and was unable to complete your request.";
			break;
	}
?>
<div id="page-name">
	Error <?php echo $code; ?>
</div>

<div id="container">
	<div id="error">
		<?php echo $message; ?>
	</div>
</div>