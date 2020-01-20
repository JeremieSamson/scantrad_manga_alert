<?php

namespace App\Service;

use App\Entity\Manga;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class EmailAlertInfo
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return int
     */
    public function getNbAlerts(){
        $total = 0;

        $mangas = $this->em->getRepository(Manga::class)->findAll();

        /** @var Manga $manga */
        foreach ($mangas as $manga){
            $total += $manga->getEmailAlerts()->count();
        }

        return $total;
    }
}