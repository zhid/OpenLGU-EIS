<?php

Class LoginConfirmationController extends CController
{
	public $breadcrumbs;
	public $defaultAction = 'login';
	public $loginError;
	
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}
	
	public function actionLogin()
	{
		$model = new LoginConfirmation();
		
		if(!Yii::app()->user->isGuest)
		{
			$this->redirect('index.php/main');
		}
		
		if(isset($_POST['LoginConfirmation']))
		{
			$model->username = $_POST['LoginConfirmation']['username'];
			$model->password = $_POST['LoginConfirmation']['password'];
		
			if($model->validate())
			{
				$this->loginError = $model->authenticate($model->username, $model->password);
				
				if($this->loginError == "NONE")
				{
					$this->redirect('index.php/main');
				}
			}
		}
		
		$breadcrumbs = "";
		
		$this->render('login', array('model'=>$model));
	}
}