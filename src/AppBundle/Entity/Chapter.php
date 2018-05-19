<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\NameTrait;
use AppBundle\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Chapter
 *
 * @ORM\Table(name="chapter")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChapterRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Chapter
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
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var Manga
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Manga", inversedBy="chapters")
     */
    private $manga;

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
     * Set number
     *
     * @param integer $number
     *
     * @return Chapter
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return Manga
     */
    public function getManga()
    {
        return $this->manga;
    }

    /**
     * @param Manga $manga
     */
    public function setManga($manga)
    {
        $this->manga = $manga;
    }
}

