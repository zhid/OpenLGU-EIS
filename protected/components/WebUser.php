<?php

class WebUser extends CWebUser
{
    /**
     * Overrides a Yii method that is used for roles in controllers (accessRules).
     *
     * @param string $operation Name of the operation required (here, a role).
     * @param mixed $params (opt) Parameters for this operation, usually the object to access.
     * @return bool Permission granted?
     */
	public function __get($name)
	{
		if($this->hasState($name))
		{
			return $this->getState($name);
		}
		else
		{
			return "guest";
		}
	} 
	
    public function checkAccess($operation, $params=array())
    {
        if (empty($this->id)) 
		{
            return false;
        }
		
        $role = $this->getState("roles");
        if ($role === 'admin') 
		{
            return true;
        }
       
        return ($operation === $role);
    }
}