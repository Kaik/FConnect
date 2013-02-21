<?php

/**
 * FConnect
 *
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * Favorites entity class.
 *
 * Annotations define the entity mappings to database.
 *
 * @ORM\Entity
 * @ORM\Table(name="fconnect")
 */
class FConnect_Entity_Connections extends Zikula_EntityAccess
{

    /**
     * The following are annotations which define the id field.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The following are annotations which define the facebook user id field.
     *
     * @ORM\Column(type="bigint", options={"unsigned"=true})
     */
    private $fb_id;
	
	/**
     * The following are annotations which define the user id field.
     *
     * @ORM\Column(type="integer")
     */
    private $user_id;
	
	
	
	public function getid()
    {
        return $this->id;
    }

    public function getfb_id()
    {
        return $this->fb_id;
    }

    public function getuser_id()
    {
        return $this->user_id;
    }

    public function setfb_id($fb_id)
    {
        $this->fb_id = $fb_id;
    }

    public function setuser_id($user_id)
    {
    	$this->user_id = $user_id;	

    }

}