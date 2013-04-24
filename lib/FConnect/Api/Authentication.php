<?php
/**
 * FConnect
 */
  
/**
 * The user authentication services for the log-in process through the Facebook protocol.
 */
class FConnect_Api_Authentication extends Zikula_Api_AbstractAuthentication
{
    /**
     * The list of valid authentication methods that this module supports.
     *
     * This list is meant to be immutable, therefore it would be prudent to
     * only expose copies, and unwise to expose explicit references.
     *
     * @var array
     */
    protected $authenticationMethods = array();

    /**
     * Initialize the API instance with the list of valid authentication methods supported.
     * 
     * @return void
     */
    protected function  postInitialize() {
        parent::postInitialize();
        

        $authenticationMethod = new Users_Helper_AuthenticationMethod(
                $this->name,
                'Facebook',
                $this->__('Facebook Account'),
                $this->__('Facebook Account'),
                true
        );
		
		$settings = ModUtil::getVar($this->name);
		
		$isEnabled = $settings['isenabled'];
		
		if ($isEnabled) {
            $authenticationMethod->enableForAuthentication();
            $authenticationMethod->enableForRegistration();
        } else {
            $authenticationMethod->disableForAuthentication();
            $authenticationMethod->disableForRegistration();
        }

			

        $this->authenticationMethods['Facebook'] = $authenticationMethod;

        
    }

    /**
     * Informs the calling function whether this authmodule is reentrant or not.
     *
     * The Facebook for Zikula module is reentrant. It must redirect to the Facebook provider for authorization.
     *
     * @return bool true.
     */
    public function isReentrant()
    {
        return false;
    }

    /**
     * Indicate whether this module supports the indicated authentication method.
     * 
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     * 
     * @return boolean True if the indicated authentication method is supported by this module; otherwise false.
     * 
     * @throws Zikula_Exception_Fatal Thrown if invalid parameters are sent in $args.
     */
    public function supportsAuthenticationMethod(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            $methodName = $args['method'];
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        $isSupported = (bool)isset($this->authenticationMethods[$methodName]);

        return $isSupported;
    }

    /**
     * Indicates whether a specified authentication method that is supported by this module is enabled for use.
     * 
     * Parameters passed in $args:
     * ---------------------------
     * string $args['method'] The name of the authentication method for which support is enquired.
     *
     * @param array $args All arguments passed to this function, see above.
     * 
     * @return boolean True if the indicated authentication method is enabled by this module; otherwise false.
     * 
     * @throws Zikula_Exception_Fatal Thrown if invalid parameters are sent in $args.
     */
    public function isEnabledForAuthentication(array $args)
    {
        if (isset($args['method']) && is_string($args['method'])) {
            if (isset($this->authenticationMethods[$args['method']])) {
                $authenticationMethod = $this->authenticationMethods[$args['method']];
            } else {
                throw new Zikula_Exception_Fatal($this->__f('An unknown method (\'%1$s\') was received.', array($args['method'])));
            }
        } else {
            throw new Zikula_Exception_Fatal($this->__('An invalid \'method\' parameter was received.'));
        }

        return $authenticationMethod->isEnabledForAuthentication();
    }

    /**
     * Retrieves an array of authentication methods defined by this module, no filter for enabled as there is only one method.
     */
    public function getAuthenticationMethods(array $args = null)
    {
        return $this->authenticationMethods;
    }
    
    /**
     * Registers a user account record or a user registration request with the authentication method.
     * 
     * This is called during the user registration process to associate an authentication method provided by this authentication module
     * with a user (either a full user account, or a user's registration request).
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   'authentication_method' An array identifying the authentication method to associate with the user account or registration
     *                                      record. The array should contain two elements: 'modname' containing the authentication module's
     *                                      name (the name of this module), and 'method' containing the name of an authentication method
     *                                      defined by this module.
     * array   'authentication_info'   An array containing the authentication information for the user. For the OpenID module, this should
     *                                      contain the user's supplied id and claimed id.
     * numeric 'uid'                   The user id of the user account record or registration request to associate with the authentication method and
     *                                      authentication information.
     *
     * @param array $args All parameters passed to this function.
     * 
     * @return boolean True if the user account or registration request was successfully associated with the authentication method and 
     *                      authentication information; otherwise false.
     * 
     * @throws Zikula_Exception_Fatal Thrown if the arguments array is invalid, or the user id, authentication method, or authentication information
     *                                      is invalid.
     */
    public function register(array $args)
    {
       // we need that at all?

        return true;
    }

    /**
     * Authenticates authentication info with the authenticating source.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OpenID server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OpenID server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * @param array   $authenticationMethod An array identifying the selected authentication method by 'modname' and 'method'.
     * @param array   $authenticationInfo   An array containing the authentication information supplied by the user; for this module, a 'supplied_id'.
     * @param string  $reentrantURL         The URL to which an external OpenID Provider should return in order to reenter the authentication proces
     *                                          following a user's attempt to authenticate on the external server.
     * @param boolean $forRegistration      If true, then a simple registration request extension will be added to the OpenID authentication request, 
     *                                          asking for the user's nickname and email address.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array containing the 'claimed_id', and any optional
     *                              simple registration fields; otherwise false on authentication failure or error.
     */
    protected function internalCheckPassword(array $authenticationMethod, array $authenticationInfo, $reentrantURL = null, $forRegistration = false)
    {
    
	return false;
	
    }

    /**
     * Authenticates authentication info with the authenticating source, returning a simple boolean result.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OpenID server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OpenID server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return boolean True if the authentication info authenticates with the source; otherwise false on authentication failure or error.
     */
    public function checkPassword(array $args)
    {
        $passwordAuthenticates = false;
            
        return $passwordAuthenticates;
    }

    /**
     * Authenticates authentication info with the authenticating source, returning simple registration information.
     *
     * ATTENTION: Any function that causes this function to be called MUST specify a return URL, and therefore
     * must be reentrant. In other words, in order to call this function, there must exist a controller function
     * (specified by the return URL) that the OpenID server can return to, and that function must restore the
     * pertinent state for the user as if he never left this site! Session variables should be used to store all
     * pertinent variables, and those must be re-read into the user's state when the return URL is called back
     * by the OpenID server.
     *
     * Note that, despite this function's name, there is no requirement that a password be part of the authentication info.
     * Merely that enough information be provided in the authentication info array to unequivocally authenticate the user. For
     * most authenticating authorities this will be the equivalent of a user name and password, but--again--there
     * is no restriction here. This is not, however, a "user exists in the system" function. It is expected that
     * the authenticating authority validate what ever is used as a password or the equivalent thereof.
     *
     * This function makes no attempt to match the given authentication info with a Zikula user id (uid). It simply asks the
     * authenticating authority to authenticate the authentication info provided. No "login" should take place as a result of
     * this authentication.
     *
     * This function may be called to initially authenticate a user during the registration process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from authenticateUser() in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid).
     *
     * This function differs from login()  in that no attempt is made to match the authentication info with and map to a
     * Zikula user account. It does not return a Zikula user id (uid). In addition this function makes no attempt to
     * perform any login-related processes on the authenticating system.
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return array|boolean If the authentication info authenticates with the source, then an array is returned containing the user's 'claimed_id',
     *                              plus requested simple registration information from the OpenID server; otherwise false on authentication failure or error.
     */
    public function checkPasswordForRegistration(array $args)
    {
        $checkPasswordResult = false;
            
        return $checkPasswordResult;
    }

    /**
     * Retrieves the Zikula User ID (uid) for the given authentication info
     *
     * From the mapping maintained by this authmodule.
     *
     * Custom authmodules should pay extra special attention to the accurate association of authentication info and user
     * ids (uids). Returning the wrong uid for a given authentication info will potentially expose a user's account to
     * unauthorized access. Custom authmodules must also ensure that they keep their mapping table in sync with
     * the user's account.
     * 
     * Parameters passed in the $args array:
     * -------------------------------------
     * array   authentication_info The authentication information uniquely associated with a user. It should contain a 'claimed_id'.
     *
     * @param array $args All arguments passed to this function.
     *
     * @return integer|boolean The integer Zikula uid uniquely associated with the given authentication info;
     *                         otherwise false if user not found or error.
     */
    public function getUidForAuthenticationInfo(array $args)
    {	
	  return (int)ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid',$args['authentication_info']['fb_id']);
    }

    /**
     * Authenticates authentication info with the authenticating source, returning the matching Zikula user id.
     *
     * This function may be called to initially authenticate a user during the login process, or may be called
     * for a user already logged in to re-authenticate his password for a security-sensitive operation. This function
     * should merely authenticate the user, and not perform any additional login-related processes.
     *
     * This function differs from checkPassword() in that the authentication info must match and be mapped to a Zikula user account,
     * and therefore must return a Zikula user id (uid). If it cannot, then it should return false, even if the authentication info
     * provided would otherwise authenticate with the authenticating authority.
     *
     * This function differs from login() in that this function makes no attempt to perform any login-related processes
     * on the authenticating system. (If there is no login-related process on the authenticating system, then this and
     * login() are functionally equivalent, however they are still logically distinct in their intent.)
     *
     * @param array $args All arguments passed to this function.
     *                      array   authentication_info    The authentication info needed for this authmodule, including any user-entered password.
     *
     * @return integer|boolean If the authentication info authenticates with the source, then the Zikula uid associated with that login ID;
     *                         otherwise false on authentication failure or error.
     */
    public function authenticateUser(array $args)
    {
	  return (int)ModUtil::apiFunc($this->name, 'FacebookUser', 'get_zuid',$args['authentication_info']['fb_id']); 
    }
    
    /**
     * Retrieve the account recovery information for the specified user.
     * 
     * The array returned by this function should be an empty array (not null) if the specified user does not have any
     * authentication methods registered with the authentication module that are enabled for log-in.
     * 
     * If the specified user does have one or more authentication methods, then the array should contain one or more elements
     * indexed numerically. Each element should be an associative array containing the following:
     * 
     * - 'modname' The authentication module name.
     * - 'short_description' A brief (a few words) description or name of the authentication method.
     * - 'long_description' A longer description or name of the authentication method.
     * - 'uname' The user name _equivalent_ for the authentication method (e.g., the claimed OpenID).
     * - 'link' If the authentication method is for an external service, then a link to the user's account on that service, or a general link to the service,
     *            otherwise, an empty string (not null).
     * 
     * For example:
     * 
     * <code>
     * $accountRecoveryInfo[] = array(
     *     'modname'           => $this->name,
     *     'short_description' => $this->__('E-mail Address'),
     *     'long_description'  => $this->__('E-mail Address'),
     *     'uname'             => $userObj['email'],
     *     'link'              => '',
     * )
     * </code>
     * 
     * Parameters passed in the $arg array:
     * ------------------------------------
     * numeric 'uid' The user id of the user for which account recovery information should be retrieved.
     * 
     * @param array $args All parameters passed to this function.
     * 
     * @return An array of account recovery information.
     * 
     * @throws Zikula_Exception_Fatal Thrown if the arguments array is invalid, if 
     */
    public function getAccountRecoveryInfoForUid(array $args)
    {
       $lostUserNames = array();
        
        return $lostUserNames;
    }
}
