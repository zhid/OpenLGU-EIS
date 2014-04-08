<?php

Class EditUser extends CFormModel
{
	public $username;
	public $password;
	public $retype_password;

	public function rules()
	{
		return array (
			array('username, password', 'required'),
			array('username', 'checkusername'),
			array('retype_password', 'checkpassword'),
		);
	}
	
	public function checkusername()
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'username';
		$criteria->condition = 'username=:username';
		$criteria->params = array('username'=>$this->username);
		$count = UserIdentification::model()->count($criteria);
		
		if($count > 0 && $this->username != Yii::app()->user->username)
		{
			$this->addError('username', 'Username already taken!');
		}
	}
	
	public function checkpassword()
	{
		if($this->password != $this->retype_password)
		{
			$this->addError('retype_password', 'Please re-type the correct password!');
		}
	}
}