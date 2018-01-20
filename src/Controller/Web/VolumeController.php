<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Manga;
use AppBundle\Entity\Volume;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Console\Output\ConsoleOutput;


class VolumeController extends Controller
{
    /**
     * Change state of the volume
     *
     * Route("{manga}/changeState/{volume}", name="change_state")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Manga $manga
     * @param Volume $volume
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeStateAction(Manga $manga, Volume $volume)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        //TODO: create Voter MUST HAVE
        if($volume != null and $volume->getReleaseDate()->diff(new \DateTime("now"))->format('%R') != "-" ) {
            if ($user->getVolumes()->contains($volume)) {
                $user->popVolume($volume);
                $em->persist($user);
                $em->flush();
            } else {
                $user->addVolume($volume);
                $em->persist($user);
                $em->flush();
            }
        }

        return $this->redirectToRoute("manga_show",array('id' => $manga->getId()));
    }

}
