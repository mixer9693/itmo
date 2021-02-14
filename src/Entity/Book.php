<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as AcmeAssert;


/**
 * @AcmeAssert\ContainsBookNameAndISBN
 * @AcmeAssert\ContainsBookNameAndYear
 *
 * @ORM\Entity
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ISBN;

    /**
     * @ORM\Column(type="integer")
     */
    private $pagesNumber;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

    /**
     * @ORM\ManyToOne(targetEntity=Image::class, cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $cover;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getISBN(): ?string
    {
        return $this->ISBN;
    }

    public function setISBN(string $ISBN): self
    {
        $this->ISBN = $ISBN;

        return $this;
    }

    public function getPagesNumber(): ?int
    {
        return $this->pagesNumber;
    }

    public function setPagesNumber(int $pagesNumber): self
    {
        $this->pagesNumber = $pagesNumber;

        return $this;
    }

    /**
     * @return Collection|Author[]
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function getCover(): ?Image
    {
        return $this->cover;
    }

    public function setCover(?Image $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

}
