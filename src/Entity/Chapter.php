<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="chapter")
 * @ORM\Entity(repositoryClass="App\Repository\ChapterRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Chapter
{
    use NameTrait, TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="Manga", inversedBy="chapters")
     */
    private $manga;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setNumber($number): void
    {
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getManga(): Manga
    {
        return $this->manga;
    }

    public function setManga(Manga $manga): void
    {
        $this->manga = $manga;
    }
}

