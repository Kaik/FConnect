<?php
/**
 * FConnect
 */

/**
 * Administrator-initiated actions for the FConnect module.
 */
class FConnect_Controller_Admin extends Zikula_AbstractController
{
    /**
     * The main administration entry point.
     *
     * Redirects to the {@link modifyConfig()} function.
     *
     * @return void
     */
    public function main()
    {
        $this->redirect(ModUtil::url($this->name, 'admin', 'modifyConfig'));
    }

    /**
     * Modify configuration.
     *
     * Modify the configuration parameters of the module.
     *
     * @return string The rendered output of the modifyconfig template.
     *
     * @throws Zikula_Exception_Forbidden Thrown if the user does not have the appropriate access level for the function.
     */
    public function modifyconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission('FConnect::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }
		
		//Get users module settings
		$modulevars = ModUtil::getVar('FConnect');
		
		$url = ModUtil::url($this->name, 'admin', 'modifyconfig', $args = array(), $ssl = null, $fragment = null, $fqurl = true, $forcelongurl = false, $forcelang=false);	
		
		$fb = array();
		
		$fb['id'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getId');
		
		if($fb['id']) {
				
			$fb['me'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getMe');	
						
			$fb['perms'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getPermissions');
			
			
			if ($modulevars['contentisenabled']) {
			//will return none anyway													
			$pagesselect = ModUtil::apiFunc($this->name, 'Admin', 'getPagesSelect');					
			
			if( !array_key_exists('publish_stream', $fb['perms']['data'][0]) ||
		          !array_key_exists('manage_pages', $fb['perms']['data'][0])) {		    	
		    $fb['pageperms'] = false;
		    $fb['getpermsurl'] = ModUtil::apiFunc($this->name, 'Facebook', 'logInUrl', array('scope'=> 'manage_pages,publish_stream','gobackurl'=> $url));
			}else{
			$fb['pageperms'] = true;	
			}		
			}
			
		
			$fb['zuid'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid',$fb['id']);				
			if (!$fb['zuid']) {
				$fb['sameemail'] = ModUtil::apiFunc('Users', 'admin', 'findUsers', array('email'=> $fb['me']['email']));	
			}			
			
		}else{
			$fb['loginUrl'] =  ModUtil::apiFunc($this->name, 'Facebook', 'logInUrl', array('gobackurl'=> $url));		
		}
		
		if ($modulevars['userdataisenabled']) {
								
				$uproperties = ModUtil::apiFunc('Profile', 'user', 'getallactive');						
			
		}

		

        // Assign all the module vars
        return $this->view->assign($modulevars)
						  ->assign('usettings',ModUtil::getVar('Users'))
						  ->assign('fb', $fb)
						  ->assign('pagesselect', $pagesselect)
						  ->assign('properties',$uproperties)
            			  ->fetch('fconnect_admin_modifyconfig.tpl');
    }

    /**
     * Update the configuration.
     *
     * Save the results of modifying the configuration parameters of the module. Redirects to the module's main page
     * when completed.
     *
     * @return void
     *
     * @throws Zikula_Exception_Forbidden Thrown if the user does not have the appropriate access level for the function.
     */
    public function updateconfig()
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        // Confirm the forms authorisation key
        $this->checkCsrfToken();

        // set our module variables
        $isenabled = $this->request->getPost()->get('isenabled', false);
                       
        $appid = $this->request->getPost()->get('appid', false);
        $this->setVar('appid', $appid);

        $secretkey = $this->request->getPost()->get('secretkey', false);
        $this->setVar('secretkey', $secretkey);
		
		if (empty($appid) || empty($secretkey)) {
		$isenabled = 0;
		$this->registerError($this->__('No Facebook credidentials. Module is not active !'));	
		}
		$this->setVar('isenabled', $isenabled);
		
		$jsisenabled = $this->request->getPost()->get('jsisenabled', false);
        $this->setVar('jsisenabled', $jsisenabled);
		
		$userdataisenabled = $this->request->getPost()->get('userdataisenabled', false);
        $this->setVar('userdataisenabled', $userdataisenabled);
		
		$contentisenabled = $this->request->getPost()->get('contentisenabled', false);
        $this->setVar('contentisenabled', $contentisenabled);
		
		$managedpage = $this->request->getPost()->get('managedpage', false);
        if ($managedpage) {
        $this->setVar('managedpage', $managedpage);	
		}
		
				
		
        // the module configuration has been updated successfuly
        $this->registerStatus($this->__('Done! Saved module configuration.'));

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        $this->redirect(ModUtil::url($this->name, 'admin', 'main'));
    }

    /**
     * Modify content.
     *
     * Modify the configuration parameters of the module.
     *
     * @return string The rendered output of the modifyconfig template.
     *
     * @throws Zikula_Exception_Forbidden Thrown if the user does not have the appropriate access level for the function.
     */
    public function content()
    {
        // Security check
        if (!SecurityUtil::checkPermission('FConnect::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }
		
		//Get users module settings
		$modulevars = ModUtil::getVar('FConnect');
		
		$url = ModUtil::url($this->name, 'admin', 'modifyconfig', $args = array(), $ssl = null, $fragment = null, $fqurl = true, $forcelongurl = false, $forcelang=false);	
		
		$fb = array();
		
		$fb['id'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getId');
		
		if($fb['id']) {
				
			$fb['me'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getMe');	
						
			$fb['perms'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'getPermissions');
			
			
			if ($modulevars['contentisenabled']) {
			//will return none anyway													
			$pagesselect = ModUtil::apiFunc($this->name, 'Admin', 'getPagesSelect');					
			
			if( !array_key_exists('publish_stream', $fb['perms']['data'][0]) ||
		          !array_key_exists('manage_pages', $fb['perms']['data'][0])) {		    	
		    $fb['pageperms'] = false;
		    $fb['getpermsurl'] = ModUtil::apiFunc($this->name, 'Facebook', 'logInUrl', array('scope'=> 'manage_pages,publish_stream','gobackurl'=> $url));
			}else{
			$fb['pageperms'] = true;
			
			
			$post['capiton'] = $this->__('Capition field.');
			$post['message'] = $this->__('Message field');
			$post['name'] = $this->__('Name field');
			$post['link'] = htmlspecialchars(System::getHost());
			$post['description'] = $this->__('Description field');
			$post['picture'] = $post['link'].'/modules/FConnect/images/admin.png';
			
			
				
			}		
			}
			
		
			$fb['zuid'] = ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid',$fb['id']);				
			if (!$fb['zuid']) {
				$fb['sameemail'] = ModUtil::apiFunc('Users', 'admin', 'findUsers', array('email'=> $fb['me']['email']));	
			}			
			
		}else{
			$fb['loginUrl'] =  ModUtil::apiFunc($this->name, 'Facebook', 'logInUrl', array('gobackurl'=> $url));		
		}		

        // Assign all the module vars
        return $this->view->assign($modulevars)
						  ->assign('usettings',ModUtil::getVar('Users'))
						  ->assign('fb', $fb)
						  ->assign('post', $post)
						  ->assign('pagesselect', $pagesselect)
            			  ->fetch('fconnect_admin_modifycontent.tpl');
    }

    /**
     * Update the configuration.
     *
     * Save the results of modifying the configuration parameters of the module. Redirects to the module's main page
     * when completed.
     *
     * @return void
     *
     * @throws Zikula_Exception_Forbidden Thrown if the user does not have the appropriate access level for the function.
     */
    public function updatecontent()
    {
        // Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }

        // Confirm the forms authorisation key
        $this->checkCsrfToken();
		
		$contentisenabled = $this->request->getPost()->get('contentisenabled', false);
        $this->setVar('contentisenabled', $contentisenabled);
		
		$managedpage = $this->request->getPost()->get('managedpage', false);
        if ($managedpage) {
        $this->setVar('managedpage', $managedpage);	
		}
				
        // the module configuration has been updated successfuly
        $this->registerStatus($this->__('Done! Saved module configuration.'));

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        $this->redirect(ModUtil::url($this->name, 'admin', 'content'));
    }
	
	
	
	public function posttest()
	{
			
		// Security check
        if (!SecurityUtil::checkPermission($this->name . '::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Forbidden();
        }	
			
			
		$post['capiton'] = $this->__('Capition field.');
		$post['message'] = $this->__('Message field');
		$post['name'] = $this->__('Name field');
		$post['link'] = htmlspecialchars(System::getHost());
		$post['description'] = $this->__('Description field');
		$post['picture'] = $post['link'].'/modules/FConnect/images/admin.png';

		$fbpost_id = ModUtil::apiFunc($this->name, 'Page', 'addPost', $post);
		
		if($fbpost_id){

        $this->registerStatus($this->__('Done! Post added to page wall. Check your page to confirm'));
        $this->redirect(ModUtil::url($this->name, 'admin', 'content'));		
			
			
		}else{
		
		$this->registerError($this->__('Error! Something went wrong. Post not added to page wall. Check your page to confirm'));
        $this->redirect(ModUtil::url($this->name, 'admin', 'content'));		
			
		}
			
		
	}

}