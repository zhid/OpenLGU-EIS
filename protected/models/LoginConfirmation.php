<?php

Class LoginConfirmation extends CFormModel
{
	public $username;
	public $password;
	public $loginError;
	
	public function rules()
	{
		return array (
			array('username, password', 'required'),
		);
	}
	
	public function authenticate($attribute, $params)
	{
		$identity = new UserAuthentication($this->username, $this->password);
		$identity->authenticate();
		
		switch($identity->errorCode)
		{
			case UserAuthentication::ERROR_NONE:
				Yii::app()->user->login($identity);
				return "NONE";
				break;
			case UserAuthentication::ERROR_USERNAME_INVALID;
				return "INVALID USERNAME";
				break;
			case UserAuthentication::ERROR_PASSWORD_INVALID;
				return "INVALID PASSWORD";
				break;
		}
	}
}