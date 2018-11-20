<?php

namespace AppBundle\Service;

use AppBundle\Entity\Manga;
use Doctrine\ORM\EntityManager;

class EmailAlertInfo
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * EmailAlertInfo constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return int
     */
    public function getNbAlerts(){
        $total = 0;

        $mangas = $this->em->getRepository('AppBundle:Manga')->findAll();

        /** @var Manga $manga */
        foreach ($mangas as $manga){
            $total += $manga->getEmailAlerts()->count();
        }

        return $total;
    }
}