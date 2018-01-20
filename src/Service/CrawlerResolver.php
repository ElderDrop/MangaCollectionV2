<?php

namespace AppBundle\Service;

use AppBundle\Crawlers\Waneko;
use AppBundle\Interfaces\IMangaCrawler;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use \Symfony\Component\Form\Form as Form;
use AppBundle\Service\Mangareader;


class CrawlerResolver
{
    /**
     * @var mixed
     */
    private $url;
    private $IMangaCrawoler;
    private $urlString;
    private $em;

    /**
     * @param string $url
     * @return IMangaCrawler|null
     */
    public function getIMangaCrawler(string $url): ?IMangaCrawler
    {
        $this->setUrl($url);
        return $this->IMangaCrawoler;
    }

    /**
     * CrawlerResolver constructor.
     * @param EntityManager $em
     * @internal param string $url
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $host
     * @return IMangaCrawler|null
     */
    private function resolver(string $host): ?IMangaCrawler
    {
        switch ($host)
        {
            case 'www.mangareader.net':
                return new Mangareader($this->urlString,$this->em);
                break;
            case 'waneko.pl':
                return new Waneko($this->urlString,$this->em);
                break;
        }
        return null;
    }

    /**
     * @param mixed $url
     */
    private function setUrl($url): void
    {
        $this->urlString = $url;
        #$output= new ConsoleOutput();
        #$output->writeln(var_dump(parse_url($url)));
        $this->url = parse_url($url);
        if (!array_key_exists('host',$this->url)) throw new Exception("Bad URL");
        if ($this->em->getRepository("AppBundle:Manga")->findBy(array("url" => $url))) throw new \Exception("All ready in database");
        $this->IMangaCrawoler = $this->resolver($this->url['host']);
    }

}