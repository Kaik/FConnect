<?php
/**
 * Copyright Zikula Foundation 2009 - Zikula Application Framework
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Zikula
 * @subpackage Users
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * The system-level and database-level functions for user-initiated actions related to new account registrations.
 */
class FConnect_Api_Registration extends Zikula_AbstractApi
{

    /**
     * Creates a new users table record.
     *
     * This is an internal function that creates a new user. External calls to create either a new
     * registration record or a new users record are made to Users_Api_Registration#registerNewUser(), which
     * dispatches either this function or createRegistration(). Users_Api_Registration#registerNewUser() should be the
     * primary and exclusive function used to create either a user record or a registraion, as it knows how to
     * decide which gets created based on the system configuration and the data provided.
     *
     * ATTENTION: This is the proper place to fire an item-created hook for the user account
     * record, even though the physical database record may have been saved previously as a pending
     * registration. See the note in createRegistration().
     *
     * @param array $reginfo          Contains the data gathered about the user for the registration record.
     * @param bool  $userNotification Whether the user should be notified of the new registration or not;
     *                                          however if the user's password was created for him, then he will
     *                                          receive at least that notification without regard to this setting.
     * @param bool $adminNotification Whether the configured administrator notification e-mail address should
     *                                          be sent notification of the new registration.
     * @param string $passwordCreatedForUser The password that was created for the user either automatically or by
     *                                          an administrator (but not by the user himself).
     *
     * @return array|bool The user info, as saved in the users table; false on error.
     *
     * @see    Users_Api_Registration#registerNewUser()
     */
    public function createUser(array $reginfo)
    {

        if (!isset($reginfo) || empty($reginfo)) {
            $this->registerError(LogUtil::getErrorMsgArgs());

            return false;
        }

        // Protected method (not callable from the api), so assume that the data has been validated in registerNewUser().
        // Just check some basic things we need directly in this function.
        if (!isset($reginfo['email']) || empty($reginfo['email'])) {
            $this->registerError(LogUtil::getErrorMsgArgs());

            return false;
        }

        // Check to see if we are getting a record directly from the registration request process, or one
        // from a later step in the registration process (e.g., approval or verification)
        //if (!isset($reginfo['uid']) || empty($reginfo['uid'])) {
            // This is a record directly from the registration request process (never been saved before)

            // Protected method (not callable from the api), so assume that the data has been validated in registerNewUser().
            // Just check some basic things we need directly in this function.
           // if (!isset($reginfo['isapproved']) || empty($reginfo['isapproved'])) {
           //     $this->registerError(LogUtil::getErrorMsgArgs());

           //     return false;
           // }

            // Ensure that no user gets created without a password, and that the password is reasonable (no spaces, salted)
            // If the user is being registered with an authentication method other than one from the Users module, then the
            // password will be the unsalted, unhashed string stored in Users_Constant::PWD_NO_USERS_AUTHENTICATION.
            //$hasPassword = isset($reginfo['pass']) && is_string($reginfo['pass']) && !empty($reginfo['pass']);
            //if ($reginfo['pass'] === Users_Constant::PWD_NO_USERS_AUTHENTICATION) {
                //$hasSaltedPassword = false;
               // $hasNoUsersAuthenticationPassword = true;
            //} 
			
            //if (!$hasPassword || (!$hasSaltedPassord && !$hasNoUsersAuthenticationPassword)) {
           //     $this->registerError(LogUtil::getErrorMsgArgs());

            //    return false;
           // }

            $reginfo['uname'] = mb_strtolower($reginfo['uname']);
            $reginfo['email'] = mb_strtolower($reginfo['email']);

            $nowUTC = new DateTime(null, new DateTimeZone('UTC'));
            $nowUTCStr = $nowUTC->format('Y-m-d H:i:s');

            // Finally, save it, but first get rid of some pseudo-properties
            $userObj = $reginfo;

            // Remove some pseudo-properties
           // if (isset($userObj['isapproved'])) {
            //    unset($userObj['isapproved']);
           // }
           // if (isset($userObj['isverified'])) {
             //   unset($userObj['isverified']);
          //  }
           // if (isset($userObj['__ATTRIBUTES__']['_Users_isVerified'])) {
            //    unset($userObj['__ATTRIBUTES__']['_Users_isVerified']);
           // }
           // if (isset($userObj['verificationsent'])) {
            //    unset($userObj['verificationsent']);
           // }
           // $userObj = $this->cleanFieldsToAttributes($userObj);

            $userObj['user_regdate'] = $nowUTCStr;

            // Approved date is set no matter what approved_by will become.
            $userObj['approved_date'] = $nowUTCStr;

            // Set activated state as pending registration for now to prevent firing of update hooks after the insert until the
            // activated state is set properly further below.
            $userObj['activated'] = -32768;

			
			$dbtable = DBUtil::getTables();
        	$userscolumn = $dbtable['users_column'];
			

            // NOTE: See below for the firing of the item-create hook.
            $userObj = DBUtil::insertObject($userObj, 'users', 'uid');
			/*
            if ($userObj) {
                if (!$createdByAdminOrSubAdmin) {
                    // Current user is not admin, so moderation is off and user "self-approved" through the registration process
                    // We couldn't do this above because we didn't know the uid.
                    $userUpdateObj = array(
                        'uid'           => $userObj['uid'],
                        'approved_by'   => $userObj['uid'],
                    );
                    // Use DBUtil so we don't get an update event. The create hasn't happened yet.
                    DBUtil::updateObject($userUpdateObj, 'users', '', 'uid');
                }

                $reginfo['uid'] = $userObj['uid'];
            }
			 
            } else {
            // This is a record from intermediate step in the registration process (e.g. verification or approval)

            // Protected method (not callable from the api), so assume that the data has been validated in registerNewUser().
            // Just check some basic things we need directly in this function.
            if (!isset($reginfo['approved_by']) || empty($reginfo['approved_by'])) {
                $this->registerError(LogUtil::getErrorMsgArgs());

                return false;
            }

            $userObj = $reginfo;

            $reginfo['isapproved'] = true;

            // Use ObjectUtil so we don't get an update event. (Create hasn't happened yet.);
            ObjectUtil::deleteObjectSingleAttribute($reginfo['uid'], 'users', '_Users_isVerified');

            // NOTE: See below for the firing of the item-create hook.
        }
		*/
        if ($userObj) {
            // Set appropriate activated status. Again, use DBUtil so we don't get an update event. (Create hasn't happened yet.)
            // Need to do this here so that it happens for both the case where $reginfo is coming in new, and the case where
            // $reginfo was already in the database.
            $userUpdateObj = array(
                'uid'       => $userObj['uid'],
                'activated' => 1,
                'approved_by'   => $userObj['uid'],
            );
            DBUtil::updateObject($userUpdateObj, 'users', '', 'uid');
            $userObj['activated'] = 1;

            // Add user to default group
            $defaultGroup = ModUtil::getVar('Groups', 'defaultgroup', false);
            if (!$defaultGroup) {
                $this->registerError($this->__('Warning! The user account was created, but there was a problem adding the account to the default group.'));
            }
            $groupAdded = ModUtil::apiFunc('Groups', 'user', 'adduser', array('gid' => $defaultGroup, 'uid' => $userObj['uid']));
            if (!$groupAdded) {
                $this->registerError($this->__('Warning! The user account was created, but there was a problem adding the account to the default group.'));
            }

            // Force the reload of the user in the cache.
            $userObj = UserUtil::getVars($userObj['uid'], true);

            // ATTENTION: This is the proper place for the item-create hook, not when a pending
            // registration is created. It is not a "real" record until now, so it wasn't really
            // "created" until now. It is way down here so that the activated state can be properly
            // saved before the hook is fired.
            $createEvent = new Zikula_Event('user.account.create', $userObj);
            $this->eventManager->notify($createEvent);

            $regErrors = array();
			/*
            if ($adminNotification || $userNotification || !empty($passwordCreatedForUser)) {
                	
					
                $sitename  = System::getVar('sitename');
                $siteurl   = System::getBaseUrl();
                $approvalOrder = $this->getVar('moderation_order', Users_Constant::APPROVAL_BEFORE);

                $rendererArgs = array();
                $rendererArgs['sitename'] = $sitename;
                $rendererArgs['siteurl'] = substr($siteurl, 0, strlen($siteurl)-1);
                $rendererArgs['reginfo'] = $reginfo;
                $rendererArgs['createdpassword'] = $passwordCreatedForUser;
                $rendererArgs['admincreated'] = $createdByAdminOrSubAdmin;
                $rendererArgs['approvalorder'] = $approvalOrder;

                if ($userNotification || !empty($passwordCreatedForUser)) {
                    $notificationSent = ModUtil::apiFunc($this->name, 'user', 'sendNotification',
                                            array('toAddress'         => $userObj['email'],
                                                  'notificationType'  => 'welcome',
                                                  'templateArgs'      => $rendererArgs));

                    if (!$notificationSent) {
                        $loggedErrorMessages = $this->request->getSession()->getMessages(Zikula_Session::MESSAGE_ERROR);
                        $this->request->getSession()->clearMessages(Zikula_Session::MESSAGE_ERROR);
                        foreach ($loggedErrorMessages as $lem) {
                            if (!in_array($lem, $regErrors)) {
                                $regErrors[] = $lem;
                            }
                            $regErrors[] = $this->__('Warning! The welcoming email for the newly created user could not be sent.');
                        }
                    }
                }

                if ($adminNotification) {
                    // mail notify email to inform admin about registration
                    $notificationEmail = $this->getVar('reg_notifyemail', '');
                    if (!empty($notificationEmail)) {
                        $subject = $this->__f('New registration: %s', $userObj['uname']);

                        $notificationSent = ModUtil::apiFunc($this->name, 'user', 'sendNotification',
                                                array('toAddress'         => $notificationEmail,
                                                      'notificationType'  => 'regadminnotify',
                                                      'templateArgs'      => $rendererArgs,
                                                      'subject'           => $subject));

                        if (!$notificationSent) {
                            $loggedErrorMessages = $this->request->getSession()->getMessages(Zikula_Session::MESSAGE_ERROR);
                            $this->request->getSession()->clearMessages(Zikula_Session::MESSAGE_ERROR);
                            foreach ($loggedErrorMessages as $lem) {
                                if (!in_array($lem, $regErrors)) {
                                    $regErrors[] = $lem;
                                }
                                $regErrors[] = $this->__('Warning! The notification email for the newly created user could not be sent.');
                            }
                        }
                    }
                }
            }
			*/
            $userObj['regErrors'] = $regErrors;

            return $userObj;
        } else {
            $this->registerError($this->__('Unable to store the new user registration record.'));

            return false;
        }
    }

}
