<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\NameTrait;
use AppBundle\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Manga
 *
 * @ORM\Table(name="manga")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MangaRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Manga
{
    use NameTrait, TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Chapter", mappedBy="manga"))
     */
    private $chapters;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\EmailAlert", mappedBy="mangas")
     */
    private $emailAlerts;

    /**
     * Manga constructor.
     */
    public function __construct()
    {
        $this->chapters = new ArrayCollection();
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
     * @param Chapter $chapter
     * @return $this
     */
    public function addChapter(Chapter $chapter){
        $this->chapters->add($chapter);

        $chapter->setManga($this);

        return $this;
    }

    /**
     * @param Chapter $chapter
     * @return $this
     */
    public function deleteChapter(Chapter $chapter){
        $this->chapters->removeElement($chapter);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getChapters(){
        return $this->chapters;
    }

    /**
     * @param EmailAlert $alert
     * @return $this
     */
    public function addEmailAlert(EmailAlert $alert){
        $this->emailAlerts->add($alert);

        $alert->addManga($this);

        return $this;
    }

    /**
     * @param EmailAlert $alert
     * @return $this
     */
    public function removeEmailAlert(EmailAlert $alert){
        $this->emailAlerts->removeElement($alert);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmailAlerts(){
        return $this->emailAlerts;
    }
}

