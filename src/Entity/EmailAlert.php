<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * EmailAlert
 *
 * @ORM\Table(name="email_alert")
 * @ORM\Entity(repositoryClass="App\Repository\EmailAlertRepository")
 */
class EmailAlert
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Manga", inversedBy="emailAlerts"))
     */
    private $mangas;

    /**
     * EmailAlert constructor.
     */
    public function __construct()
    {
        $this->mangas = new ArrayCollection();
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
     * Set email
     *
     * @param string $email
     *
     * @return EmailAlert
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param Manga $manga
     * @return $this
     */
    public function addManga(Manga $manga){
        $this->mangas->add($manga);

        return $this;
    }

    /**
     * @param Manga $manga
     * @return $this
     */
    public function removeManga(Manga $manga){
        $this->mangas->removeElement($manga);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMangas(){
        return $this->mangas;
    }
}

