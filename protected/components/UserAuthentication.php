<?php

class UserAuthentication extends CUserIdentity
{
	private $id;
	
	public function authenticate()
	{
		$record = UserIdentification::model()->findByAttributes(array('username'=>$this->username));
		
		if($record == null)
		{
			$this->errorCode = self::ERROR_USERNAME_INVALID;
		}
		else if($record->password !== $this->password)
		{
			$this->errorCode = self::ERROR_PASSWORD_INVALID;
		}
		else
		{
			$this->id = $record->username;
			$this->setState('username', $record->username);
			$this->setState('roles', $record->role);
			$this->setState('area_id', $record->area_id);
			$this->setState('isGuest', false);
			$this->errorCode = self::ERROR_NONE;
		}
		
		return !$this->errorCode;
	}
	
	public function getId()
	{
		return $this->id;
	}
}