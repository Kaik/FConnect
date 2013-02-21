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
	 require_once 'modules/FConnect/lib/vendor/Facebook/facebook.php';				
	 $settings = ModUtil::getVar($this->name);
	 $this->facebook = new Facebook(array(
				'appId' => $settings['appid'],
				'secret' => $settings['secretkey']
				));	 

	}
	
  /**
 * Log in url .
 */
   public function getmyloginurl()
	{		
	 $redirecturi = ModUtil::url($this->name, 'user', 'main', $args = array(), $ssl = null, $fragment = null, $fqurl = true, $forcelongurl = false, $forcelang=false);		
	 return $this->facebook->getLoginUrl($params = array('scope' => 'email,read_stream','redirect_uri' => $redirecturi));					
	}
	
  /**
 * FBID .
 */
	public function getmyfb_id()
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
  /**
 * User Data .
 */
	public function getmyfb_userdata()
	{
	   return $this->facebook->api('/me');		
	}
	
	
  /**
 * Login .
 */
	public function logmein($fb_id)
	{
			
		$authenticationInfo = array(
              'fb_id' => $fb_id,
              'pass'     => false,
          );
    	$authenticationMethod = array(
             'modname'   => 'FConnect',
             'method'    => 'Facebook'
          );
 
     return UserUtil::loginUsing($authenticationMethod, $authenticationInfo, $rememberme, null, false);
	
	}
  /**
 * find connected users
 */
	public function get_myuid_bymyfb_id($fb_id)
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
  /**
 * Setup connection private?
 */
	public function connectme($fb_id,$user_id)
	{
				
		$connection = new FConnect_Entity_Connections();
		$connection->setFb_id($fb_id);
		if (is_numeric($user_id)){
		$connection->setUser_id($user_id);	
		}else{
		$connection->setUser_id(UserUtil::getVar('uid'));	
		}
		
		$this->entityManager->persist($connection);
		$this->entityManager->flush();	
		
	  return true;
	}
	
	  /**
 * Setup connection private?
 */
	public function getunnamefromemail($email)
	{
		
		$aaa = explode('@',$email);
		$basename = $a[0];
	
	  return $this->generateuname($basename);
	}
	
	/**
 * Setup connection private?
 */
	public function generateuname($basename)
	{

	$umaneUsageCount = UserUtil::getUnameUsageCount($basename);   
    	if ($umaneUsageCount) {			
			$basename = $basename . '323';
		$basename =	$this->generateuname($basename);
		}
			
	  return $basename;
	}	
	
	
	
	
  /**
 * Register me return user id .
 */
	public function registerme()
	{
		
		$fb_id = $this->facebook->getUser(); 
		//we need reg info so lets get it form facebook
		//email first
		$user_data = $this->getmyfb_userdata();
		
		if ($user_data){		
			$email = $user_data['email'];
			//check if email is registered
			//we should check first for the email settings in users module
			//if ($this->getVar(Users_Constant::MODVAR_REQUIRE_UNIQUE_EMAIL, false)) {
				
			$emailUsageCount = UserUtil::getEmailUsageCount($email);
            //}
            if ($emailUsageCount) {
    		//get uid of user actually using this email
    		$user_to_connect = ModUtil::apiFunc($this->name, 'admin', 'findUsers', array('email'=> $email));
    		//connect

			
			$this->connectme($fb_id,$user_to_connect);
			
			return true;
            }

			//process email for registration
			
			$uname = $this->getunnamefromemail($email);
			
			$reginfo = array(
		     'uname'         => $uname,
		     'pass'          => 'NO_USERS_AUTHENTICATION',
		     'passreminder'  => 'Account created with Facebook',
		     'email'         => $email,
		     );	
				
			$registeredObj = ModUtil::apiFunc('Users', 'registration', 'registerNewUser', array(
		                          'reginfo'           => $reginfo,
		                         'usernotification'  => true,
		                         'adminnotification' => true
		                      ));
			
			$verified = ModUtil::apiFunc('Users', 'registration', 'verify', array('reginfo' => $registeredObj));	
		
	    	$this->connectme($fb_id,$verified['uid']);
	    
	    	return true;
	  	
		}
		
	 return false;
	  
	}
	
}