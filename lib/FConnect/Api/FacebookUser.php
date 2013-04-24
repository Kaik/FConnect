<?php
/**
 * FConnect
 */
	
/**
 * Facebook User API functions.
 */
 

class FConnect_Api_FacebookUser extends Zikula_AbstractApi
{
  
   private $facebook;
   
	
   protected function initialize() 
   {	 					
	 $this->facebook = ModUtil::apiFunc($this->name, 'Facebook', 'facebook');  
   }
   
   public function getId()
   {   
	$fb_id = $this->facebook->getUser();
		
		if ($fb_id) {
			try {
		  		$user_profile = $this->facebook->api('/me');		  	
			} catch (FacebookApiException $e) {
		  		$fb_id = null;
			}
		}
		
	 return $fb_id;			
   }   
   	
   public function getPermissions()
   {		
	return $this->facebook->api("/me/permissions");  
   }
   
   public function getMe()
   {		
	return $this->facebook->api('/me');   
   }
   
   public function getEmail()
   {		
	return $this->facebook->api('/me/email');   
   }
   
    public function getAvatar()
   {		
	return $this->facebook->api('/me');   
   }
   
   public function getPages()
   {
   	return $this->facebook->api('/me/accounts');	
   }
   
   public function get_zuid($fb_id)
	{
				
		$connection = $this->entityManager->getRepository('FConnect_Entity_Connections')
	                                   ->findOneBy(array('fb_id' => $fb_id));
		if (!$connection) {
	    $con = false;
	    }else {                         
	    $con = $connection->toArray();	
	    }
	
	  return $con['user_id']; 
	}
	
	public function set_zuid($args = false)
	{
				
		$connection = new FConnect_Entity_Connections();
		
		$connection->setFb_id($args['fb_id']);
		
		if (is_numeric($args['z_uid'])){
		$connection->setUser_id($args['z_uid']);	
		}else{
		$connection->setUser_id(UserUtil::getVar('uid'));	
		}
		
		$this->entityManager->persist($connection);
		$this->entityManager->flush();	
		
	  return true;
	}
}