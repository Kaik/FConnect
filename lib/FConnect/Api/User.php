<?php
/**
 * FConnect
 */
	
/**
 * Administrative API functions.
 */
 

class FConnect_Api_User extends Zikula_AbstractApi
{
	
   private $facebook;
   
	
	protected function initialize() 
	{	 					
	 $this->facebook = ModUtil::apiFunc($this->name, 'Facebook', 'facebook');  
	}


	public function login($fb_id)
	{
			
		$authenticationInfo = array(
              'fb_id' => $fb_id,
              'pass'     => false,
          );
		  
    	$authenticationMethod = array(
             'modname'   => 'FConnect',
             'method'    => 'Facebook'
          );
		  
		$uid = ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid', $fb_id); 	  
 		
		//clean this to eliminate error's on first log in 
		LogUtil::getErrorMessages(true);
		
     	return UserUtil::loginUsing($authenticationMethod, $authenticationInfo, true, null, false, $uid);
	
}
	
	
	public function register()
	{
		
		$fb_id = ModUtil::apiFunc($this->name, 'FacebookUser', 'getId'); 
		//we need reg info so lets get it form facebook
		//email first
		$me = ModUtil::apiFunc($this->name, 'FacebookUser', 'getMe');
		
		if ($me){
						
			$email = $me['email'];
			//check if email is registered
			//we should check first for the email settings in users module
			//if ($this->getVar(Users_Constant::MODVAR_REQUIRE_UNIQUE_EMAIL, false)) {
				
			$emailUsageCount = UserUtil::getEmailUsageCount($email);
            if ($emailUsageCount) {
    			//get uid of user actually using this email
    			// should be only for email strict mode
    			// what if there is more accounts with same email? create another one?
    			$user_to_connect = ModUtil::apiFunc('Users', 'admin', 'findUsers', array('email'=> $email));
    			//there can be only one :)
    			$user_to_connect_id = $user_to_connect[0]['uid'];
    			
				ModUtil::apiFunc($this->name, 'FacebookUser', 'set_zuid', array('fb_id'=> $fb_id,'z_uid'=> $user_to_connect_id));
			
				return true;
            }
			
			//no email used
			//process email			
			//generate uname
			$basename = $this->getunnamefromemail($email);			
			$uname = $this->generateuname($basename);
						
			// valid uname and email proceed to 
			$reginfo = array(
		     'uname'         => $uname,
		     'pass'          => 'NO_USERS_AUTHENTICATION',
		     'passreminder'  => 'Account created with Facebook',
		     'email'         => $email,
		     );
			 					
			$registeredObj = ModUtil::apiFunc('FConnect', 'registration', 'createUser', $reginfo);
					
			ModUtil::apiFunc($this->name, 'FacebookUser', 'set_zuid', array('fb_id'=> $fb_id,'z_uid'=> $registeredObj['uid']));
										  		
			
			$this->avatar();
	    
	    	return true;
	  	
		}

		//Error no user data
	 return false;
	  
	}
		
	
   public function avatar()
   {
									
		$fb_id = ModUtil::apiFunc($this->name, 'FacebookUser', 'getId');
		
		if (isset($fb_id)) {
		$uid = ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid', $fb_id);		
		} else {		
		return false;					
		}	
	
		$img = file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture?type=large');
		
		$extension = 'jpg';
		
		$avatarpath = ModUtil::getVar('Users', 'avatarpath');
        $avatarfilenamewithoutextension = 'pers_' . $uid;
        $avatarfilename = $avatarfilenamewithoutextension . '.' . $extension;
        $user_avatar = DataUtil::formatForOS($avatarpath . '/' . $avatarfilename);

		file_put_contents($user_avatar, $img);
				
		UserUtil::setVar('avatar', $avatarfilename, $uid);
		
		return true;
	}
	



 /**
 * Get part?
 */
	private function getunnamefromemail($email)
	{
			
		$email = explode('@',$email);
		$basename = $email[0];
				
	  return $this->validatebasename($basename);
	}	
 /**
 *  fix basename lenght illegal characters etc..
 */
	private function validatebasename($basename)
	{			
	  return $basename;
	}		
/**
 * uname need to be unique
 */
	private function generateuname($basename)
	{
		
		$umaneUsageCount = UserUtil::getUnameUsageCount($basename);   
    	if ($umaneUsageCount) {			
			$basename = $basename . 'x';
			$basename =	$this->generateuname($basename);
		}
			
	  return $basename;
	}		
}