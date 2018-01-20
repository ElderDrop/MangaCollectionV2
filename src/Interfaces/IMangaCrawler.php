<?php

namespace AppBundle\Interfaces;
use AppBundle\Entity\Manga as Manga;

/**
 * Interfaces IMangaCrawler
 */

interface IMangaCrawler
{
    public function getManga(): Manga;

}