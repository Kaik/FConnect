<?php
/**
 * FConnect
 */
	
/**
 * Facebook API functions.
 */
 

class FConnect_Api_Facebook extends Zikula_AbstractApi
{
   
   	protected $_facebook;

	protected $_session;

	protected $_me;
	
   protected function initialize() 
	{
				
     require_once 'modules/FConnect/lib/vendor/Facebook/facebook.php';				
	 $settings = ModUtil::getVar($this->name);
	 $this->_facebook = new Facebook(array(
				'appId' => $settings['appid'],
				'secret' => $settings['secretkey']
				));
	
	}
	
	public function app_id()
	{
		return $this->_facebook->getAppId();
	}

	public function logged_in()
	{
		return $this->_me != NULL;
	}

	public function user_id()
	{
		return $this->_facebook->getUser();
	}

	public function session()
	{
		return $this->_session;
	}

	public function account()
	{
		return $this->_me;
	}

	public function facebook()
	{
		return $this->_facebook;
	}
	
   public function logInUrl($args = null)
	{		
		if($args['gobackurl'] == null){			
		$args['gobackurl'] = ModUtil::url($this->name, 'user', 'main', $args = array(), $ssl = null, $fragment = null, $fqurl = true, $forcelongurl = false, $forcelang=false);				
		}
				
		if($args['scope'] == null){			
		$args['scope'] = 'email,read_stream';
		}		
		
	 return $this->_facebook->getLoginUrl($params = array('scope' => $args['scope'],'redirect_uri' => $args['gobackurl']));						
	}
		
}