<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Volume", inversedBy="users",cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $volumes;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->volumes = new ArrayCollection();
    }

    /**
     * Volume getter
     * @return mixed
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * Volume setter
     * @param mixed $volumes
     */
    public function setVolumes($volumes): void
    {
        $this->volumes = $volumes;
    }

    /**
     * Adds volume to this user
     * @param Volume $volume
     */
    public function addVolume(Volume $volume):void
    {
        $volume->addUser($this);
        $this->volumes[] = $volume;
    }

    /**
     * Check is this user have got in datebase volume form param
     * @param Volume $volume
     * @return bool
     */
    public function haveVolume(Volume $volume)
    {
        foreach ($this->volumes as $v){
            if ($v == $volume) return true;
        }
        return false;
    }

    /**
     * Search for volume to be unlink form the databse
     * @param Volume $volume
     */
    public function popVolume(Volume $volume)
    {
        $newArray = new ArrayCollection();
        foreach ($this->volumes as $v)
        {
            if($v != $volume) $newArray[] = $v;
        }
        $this->volumes = $newArray;
    }
}