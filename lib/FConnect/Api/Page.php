<?php
/**
 * FConnect
 */
	
/**
 * Administrative API functions.
 */
 

class FConnect_Api_Page extends Zikula_AbstractApi
{
	
   private $facebook;
   private $pageid;
   private $access_token;
	
   protected function initialize() 
   {
	 					
	 $this->facebook = ModUtil::apiFunc($this->name, 'Facebook', 'facebook');
   	 $this->pageid = ModUtil::getVar('FConnect','managedpage');
	 $page = $this->facebook->api("/$this->pageid?fields=access_token");
	 $this->access_token = $page['access_token'];

   
   }
   
   	public function getAccessToken()
	{

	  return $this->access_token;

	}
   

	public function getPage()
	{



	}	


	public function addPost($post)
	{
		

	$args = array('access_token'  => $this->access_token,
				  'caption' 	  => $post['capiton'],
			      'message' 	  => $post['message'],
			      'name' 		  => $post['name'],
			      'link' 		  => $post['link'],
			      'description'   => $post['description'],
			      'picture' 	  => $post['picture']
	);				

	$post_id = $this->facebook->api("/$this->pageid/feed","post",$args);
	
	return $post_id;
	
	}
	


		
	/*
	 * 
	 	public function publish_on_page()
	{

	$user = $this->facebook->getUser();
	

	if($user){
	    try{
	        $user_profile = $this->facebook->api('/me');
	        $access_token = $this->facebook->getAccessToken();
	        $vars = array(
	        'caption' => 'test',
	        'message' => 'Hey guys',
	        'name' => 'Test',
	        'link' => 'http://www.example.com',
	        'description' => 'ajjj',
	        'picture' => 'http://fbrell.com/f8.jpg'
	        );
			
			
	        $result = $this->facebook->api('/135258589894475/feed?access_token='.$access_token, 'post', $vars);
	        if($result){
	           $message = 'Post was set';
	        }
	        else{
	            $message = 'Error!';
	        }
	    }
	    catch(FacebookApiException $e){
	       	LogUtil::registerError($e->getMessage());
	        $user = NULL;
	    }
	}else{
	    $message = '<a href="'.$loginUrl.'"><img src="img/login.png"/></a>';
	}
	
		return $message;
	
	}
	 * 
	 * 
	public function publish_on_pagx()
	{	
		$message = array(
			        'caption' => 'test',
			        'message' => 'Hey guys',
			        'name' => 'Test',
			        'link' => 'http://www.example.com',
			        'description' => 'ajjj',
			        'picture' => 'http://fbrell.com/f8.jpg'
			        );
		
		
		
		// Get User ID
		$user = $this->facebook->getUser();
		
		if ($user) {
		  try {
		    $page_info = $this->facebook->api("/$page_id?fields=access_token");
		    
		    if( !empty($page_info['access_token']) ) {
		        $args = array(
		            'access_token'  => $page_info['access_token'],
		            'message'       => $message 
		        );
		        $post_id = $this->facebook->api("/$page_id/feed","post",$args);
		    
			
			
			
			
			} else {
		        $permissions = $this->facebook->api("/me/permissions");
		        if( !array_key_exists('publish_stream', $permissions['data'][0]) ||
		           !array_key_exists('manage_pages', $permissions['data'][0])) {
		               LogUtil::registerError($permissions);
		        }
		    }
		  } catch(FacebookApiException $e){
	       	LogUtil::registerError($e->getMessage());
	        $user = NULL;
	    }
		}
		// Login or logout url will be needed depending on current user state.
		if ($user) {
		  $logoutUrl = $this->facebook->getLogoutUrl();
		} else {
		  $loginUrl = $this->facebook->getLoginUrl(array('scope'=>'manage_pages,publish_stream'));
		}
		// ... rest of your code

	return $loginUrl;
	 * 
		

	public function publish_on_pagx()
	{	

		$page_id = '135258589894475';
		$message = array(
			        'caption' => 'test',
			        'message' => 'Hey guys',
			        'name' => 'Test',
			        'link' => 'http://www.example.com',
			        'description' => 'ajjj',
			        'picture' => 'http://fbrell.com/f8.jpg'
			        );
		
		
		
		// Get User ID
		$user = $this->facebook->getUser();
		
		if ($user) {
		  try {
		    $page_info = $this->facebook->api("/$page_id?fields=access_token");
		    
		    if( !empty($page_info['access_token']) ) {
		        $args = array(
		            'access_token'  => $page_info['access_token'],
		            'message'       => $message 
		        );
		        $post_id = $this->facebook->api("/$page_id/feed","post",$args);
		    
			
			
			
			
			} else {
		        $permissions = $this->facebook->api("/me/permissions");
		        if( !array_key_exists('publish_stream', $permissions['data'][0]) ||
		           !array_key_exists('manage_pages', $permissions['data'][0])) {
		               LogUtil::registerError($permissions);
		        }
		    }
		  } catch(FacebookApiException $e){
	       	LogUtil::registerError($e->getMessage());
	        $user = NULL;
	    }
		}
		// Login or logout url will be needed depending on current user state.
		if ($user) {
		  $logoutUrl = $this->facebook->getLogoutUrl();
		} else {
		  $loginUrl = $this->facebook->getLoginUrl(array('scope'=>'manage_pages,publish_stream'));
		}
// ... rest of your code

	return $loginUrl;

}
	
   public function getmypagelogin($customurl = null)
	{		
		if($customurl != null){
		$redirecturi = $customurl;		
		}else {		
		$redirecturi = ModUtil::url($this->name, 'user', 'main', $args = array(), $ssl = null, $fragment = null, $fqurl = true, $forcelongurl = false, $forcelang=false);				
		}
		
		$scope = 'manage_pages,publish_stream';
		
		
	 return $this->facebook->getLoginUrl($params = array('scope' => $scope,'redirect_uri' => $redirecturi));						
	}	
	}	
	*/	
}