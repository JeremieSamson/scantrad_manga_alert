<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="manga")
 * @ORM\Entity(repositoryClass="App\Repository\MangaRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Manga
{
    use NameTrait, TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Chapter", mappedBy="manga"))
     */
    private $chapters;

    /**
     * @ORM\ManyToMany(targetEntity="EmailAlert", mappedBy="mangas")
     */
    private $emailAlerts;

    public function __construct()
    {
        $this->chapters = new ArrayCollection();
        $this->emailAlerts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function addChapter(Chapter $chapter): void
    {
        $this->chapters->add($chapter);

        $chapter->setManga($this);
    }

    public function deleteChapter(Chapter $chapter):void
    {
        $this->chapters->removeElement($chapter);
    }

    public function getChapters(): Collection
    {
        return $this->chapters;
    }

    public function addEmailAlert(EmailAlert $alert): void
    {
        if (!$this->emailAlerts->contains($alert)) {
            $this->emailAlerts->add($alert);
            $alert->addManga($this);
        }
    }

    public function removeEmailAlert(EmailAlert $alert): void
    {
        $this->emailAlerts->removeElement($alert);
    }

    public function getEmailAlerts(): Collection
    {
        return $this->emailAlerts;
    }
}

