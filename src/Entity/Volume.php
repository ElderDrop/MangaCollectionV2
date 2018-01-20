<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Volume
 *
 * @ORM\Table(name="volume")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VolumeRepository")
 */
class Volume
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="releaseDate", type="datetime", nullable=true)
     */
    private $releaseDate;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Manga", inversedBy="volumes")
     */
    private $manga;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="volumes")
     */
    private $users;

    /**
     * Volume constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Volume
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set releaseDate
     *
     * @param \DateTime $releaseDate
     *
     * @return Volume
     */
    public function setReleaseDate($releaseDate)
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * Get releaseDate
     *
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @return string
     */
    public function getManga(): string
    {
        return $this->manga;
    }

    /**
     * @param string $manga
     */
    public function setManga(Manga $manga)
    {
        $this->manga = $manga;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    public function popUser(User $user)
    {
        if(!$this->users->contains($user))return;
        $this->users->removeElement($user);
    }
}

