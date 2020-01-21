<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="email_alert")
 * @ORM\Entity(repositoryClass="App\Repository\EmailAlertRepository")
 */
class EmailAlert
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Manga", inversedBy="emailAlerts"))
     */
    private $mangas;

    public function __construct()
    {
        $this->mangas = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function addManga(Manga $manga): void
    {
        $this->mangas->add($manga);
    }

    public function removeManga(Manga $manga): void
    {
        $this->mangas->removeElement($manga);
    }

    public function getMangas(): Collection
    {
        return $this->mangas;
    }
}

