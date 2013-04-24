<?php
/**
 * FConnect
 */

/**
 * Administrative API functions.
 */
class FConnect_Api_Admin extends Zikula_AbstractApi
{

    /**
     * Get available admin panel links.
	 */
    public function getPagesSelect()
    {
		
		$pages = ModUtil::apiFunc($this->name, 'FacebookUser', 'getPages');

		$select = array();
		$select[0] = 'None';
		foreach ($pages['data'] as $page) {
		$select[$page['id']] = $page['name'];
        }		
		return $select;	
    }



    /**
     * Get available admin panel links.
     *
     * @return array Array of adminpanel links.
     */
    public function getLinks()
    {
        $links = array();
		
		$modulevars = ModUtil::getVar('FConnect');

        if (SecurityUtil::checkPermission('FConnect::', '::', ACCESS_ADMIN)) {
            $links[] = array('url' => ModUtil::url($this->name, 'admin', 'modifyConfig'), 'text' => $this->__('Settings'), 'class' => 'z-icon-es-home');
        }
		
		
		if (SecurityUtil::checkPermission('FConnect::', '::', ACCESS_ADMIN) && $modulevars['userdataisenabled']) {
            $links[] = array('url' => ModUtil::url($this->name, 'admin', 'userdata'), 'text' => $this->__('User Informations'), 'class' => 'z-icon-es-user');
        }
		
		if (SecurityUtil::checkPermission('FConnect::', '::', ACCESS_ADMIN) && $modulevars['contentisenabled']) {
            $links[] = array('url' => ModUtil::url($this->name, 'admin', 'content'), 'text' => $this->__('Page content creation'), 'class' => 'z-icon-es-copy');
        }

        return $links;
    }
}