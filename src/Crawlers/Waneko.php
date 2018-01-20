<?php
/**
 * Created by PhpStorm.
 * User: jolszanski
 * Date: 06.10.17
 * Time: 22:27
 */

namespace AppBundle\Crawlers;



use AppBundle\Entity\Genre;
use AppBundle\Entity\Manga as Manga;
use AppBundle\Entity\Status;
use AppBundle\Entity\Volume;
use AppBundle\Interfaces\IMangaCrawler;
use AppBundle\Service\GenreService;
use AppBundle\Service\StatusService;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use \Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Validator\Constraints\DateTime;


class Waneko implements IMangaCrawler
{

    private $em;
    private $crawler;
    private $urlString;
    private $url;
    private $genreService;

    /**
     * Mangareader constructor. Parse string to url format and init the crowaler form that url
     * @param string $url
     * @param EntityManager $em
     */
    public function __construct(string $url,EntityManager $em)
    {

        $this->em = $em;

        $this->urlString = $url;
        $this->url = parse_url($url);


        $client = new Client();
        $this->crawler = $client->request('GET',$url);
    }

    /**
     * Creates manga object
     * @return Manga
     */
    public function getManga(): Manga
    {
        $manga = new Manga();
        $manga->setTitle($this->getTitle());
        $manga->setAuthor($this->getAuthor());
        $manga->addGenres($this->getGenres(new GenreService($this->em)));
        $manga->addStatus($this->getStatus());
        $manga->addVolumes($this->getVolumes());
        $manga->setUrl($this->urlString);
        return $manga;
    }


    /**
     * Crawler the title for the crawler
     * @return string
     */
    private function getTitle(): string
    {
        return $this->crawler->filter('.main-content h1')->text();
    }

    /**
     * Gets genres from the page and if it is'n in db, it also uploads it.
     * @param GenreService $gs
     * @return array
     */
    private function getGenres(GenreService $gs): ?array
    {
        $this->genreService = $gs;
        $genreFound = false;
        $genreText = $this->crawler->filter('.col-8-tablet p')->each(
            function (Crawler $node) use (&$genreFound){
                if(!$genreFound && strrchr((string)$node->text(),"Gatunek")){
                    $genreFound = true;
                    return (string)$node->text();
            }
        });

        if(count($genreText) == 1 && is_null($genreText[0]))return null;

        foreach ($genreText as $text)
        {
            if (!is_null($text))
            {
                $genreText = $text;
                break;
            }
        }

        $genres = explode(',',substr($genreText,strrpos($genreText,":")+1));
        foreach ($genres as &$genre)
        {
            // TODO:// Toos this part to genre object / repository
            $genre = strtolower(trim($genre));
            if($genre != "" && !$this->genreService->contains($genre)) {
                $this->prepareGenreToSaveAndSaveIt($genre);
            }
            $genre = $this->em->getRepository('AppBundle:Genre')->findOneByName($genre);
        }
        return $genres;

    }

    /**
     * Creates new genre and pushing it to DB
     * @param string $genreName
     * @internal param string $genre
     */
    private function prepareGenreToSaveAndSaveIt(string $genreName): void
    {
        $genre = new Genre();
        $genre->setName(ucwords(strtolower($genreName)));

        $genre->addLanguage($this->em->getRepository('AppBundle:Language')->findOneBy(array('languageShortName' => 'pl')));

        $this->em->persist($genre);
        $this->em->flush();

    }

    /**
     *TODO:// Finish this function
     * @return Status
     */
    public function getStatus():Status
    {
        $statusService = new StatusService($this->em);

        $newStatus = $this->em->getRepository('AppBundle:Status')->find(1);

        return $newStatus;
    }

    /**
     * Crawlers all volumes from the main crawler
     * @return array
     */
    private function getVolumes():array
    {
        $volumes = $this->crawler->filter('.related-toms')->children()->each(
            function (Crawler $node ){
            $client = new Client();
            $volumeCrawler = $client->request('GET',$node->filter("a")->attr("href"));
            $volume = new Volume();
            $titleString = $volumeCrawler->filter('.main-content h1')->text();
            $volume->setNumber((int) substr($titleString,(strripos($titleString,"TOM")+3)));
            if($volumeCrawler->filter('table tr:nth-child(3) td')->first()->text() == "Data premiery:"){
                $volume->setReleaseDate(new \DateTime($volumeCrawler->filter('table tr:nth-child(3) td')->last()->text()));
            }else{
                $volume->setReleaseDate(null);
            }
            return $volume;
        });
        return $volumes;
    }

    /**
     * Finds Author from the first volume of the manga
     * @return string
     */
    private function getAuthor():string
    {
        $client = new Client();

        $volumeCrawler = $client->request('GET',$this->crawler->filter('.related-toms')->children()->first()->filter("a")->attr("href"));
        if($volumeCrawler->filter('table tr:nth-child(1) td')->first()->text() == "Autor:")
        {
            return $volumeCrawler->filter('table tr:nth-child(1) td')->last()->text();
        }
    }
}