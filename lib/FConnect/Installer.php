<?php

/**
 * FConnect
 */
Class FConnect_Installer extends Zikula_AbstractInstaller
{

    private $_entities = array(
        'FConnect_Entity_Connections'
    );

    /**
     *  Initialize a new install of the FConnect module
     *
     *  This function will initialize a new installation of Dizkus.
     *  It is accessed via the Zikula Admin interface and should
     *  not be called directly.
     */
    public function install()
    {

        try {
            DoctrineHelper::createSchema($this->entityManager, $this->_entities);
        } catch (Exception $e) {
            return LogUtil::registerError($e->getMessage());
        }


        // Initialisation successful
        return true;
    }

    /**
     *  Deletes an install of the FConnect module
     *
     *  This function removes Dizkus from your
     *  Zikula install and should be accessed via
     *  the Zikula Admin interface
     */
    public function uninstall()
    {
        try {
            DoctrineHelper::dropSchema($this->entityManager, $this->_entities);
        } catch (Exception $e) {
          return LogUtil::registerError($e->getMessage());  
        }

        // remove module vars
        $this->delVars();

        // Deletion successful
        return true;
    }

    public function upgrade($oldversion)
    {

    }
}
