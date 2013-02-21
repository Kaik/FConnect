<?php

/**
 * FConnect
 */

/**
 * Provides metadata for this module to the Extensions module.
 */
class FConnect_Version extends Zikula_AbstractVersion
{

    /**
     * Assemble and return module metadata.
     *
     * @return array Module metadata.
     */
    public function getMetaData()
    {
        $meta = array();
        $meta['displayname'] = $this->__('FConnect');
        $meta['description'] = $this->__('FConnect Zikula Facebook integration module');
        $meta['url'] = $this->__('FConnect');
        $meta['version'] = '1.0.0';
        $meta['core_min'] = '1.3.4'; // Fixed to 1.3.x range
        $meta['core_max'] = '1.3.99'; // Fixed to 1.3.x range
        $meta['contact'] = 'http://kaikmedia.com';
        $meta['securityschema'] = array('FConnect::' => '::');
		$meta['capabilities']  = array('authentication' => array(
                    				   'version'   => '1.0.0'));
        return $meta;
    }

}
