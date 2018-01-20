<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Console\Output\ConsoleOutput;
use JMS\Serializer\Annotation\Groups;

/**
 * Manga
 *
 * @ORM\Table(name="manga")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MangaRepository")
 */
class Manga
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"list","show"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Groups({"list","show"})
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="Status", inversedBy="mangas")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     * @Groups({"list","show"})
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="views", type="string", length=255, options={"default" : 0})
     * @Groups({"list","show"})
     */
    private $views;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="mangas")
     * @Groups({"list","show"})
     */
    private $genres;

    /**
     * @ORM\Column(name="crawler_url", type="string", length=255)
     */
    private $url;

    /**
     *
     * @ORM\OneToMany(targetEntity="Volume", mappedBy="manga",cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="manga_id", referencedColumnName="id")
     */
    private $volumes;
    /**
     * Manga constructor.
     */
    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->volumes = new ArrayCollection();
        $this->views = 0;
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
     * Set title
     *
     * @param string $title
     *
     * @return Manga
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Manga
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Manga
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set views
     *
     * @param string $views
     *
     * @return Manga
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return string
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @return mixed
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param mixed $genres
     */
    public function setGenres($genres)
    {
        $this->genres = $genres;
    }

    /**
     * @param Genre $genre
     */
    public function addGenre(Genre $genre)
    {
        $genre->addManga($this);
        $this->genres[] = $genre;
    }

    /**
     * @param array|null $genres
     * @return null
     */
    public function addGenres(?array $genres)
    {
        if(is_null($genres)) return null;
        foreach ($genres as $genre)
        {
            $genre->addManga($this);
            $this->genres[] = $genre;
        }

    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param Status $status
     */
    public function addStatus(Status $status):void
    {
        $status->addManga($this);
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     *
     * @param Volume $volume
     */
    public function addVolume(Volume $volume): void
    {
        $this->volumes[] = $volume;
    }

    /**
     * @param array $volumes
     */
    public function addVolumes(array $volumes):void
    {
        foreach ($volumes as $volume)
        {
            $volume->setManga($this);
            $this->volumes[] = $volume;
        }
    }
}

