<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Article;

/**
 * Categ
 *
 * @ORM\Table(name="categ", uniqueConstraints={@ORM\UniqueConstraint(name="slug_UNIQUE", columns={"slug"})})
 * @ORM\Entity
 */
class Categ
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcateg", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcateg;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=80, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=80, nullable=false)
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descr", type="string", length=300, nullable=true)
     */
    private $descr;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Article", inversedBy="categIdcateg")
     * @ORM\JoinTable(name="categ_has_article",
     *   joinColumns={
     *     @ORM\JoinColumn(name="categ_idcateg", referencedColumnName="idcateg")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="article_idarticle", referencedColumnName="idarticle")
     *   }
     * )
     */
    private $articleIdarticle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->articleIdarticle = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getIdcateg(): ?int
    {
        return $this->idcateg;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescr(): ?string
    {
        return $this->descr;
    }

    public function setDescr(?string $descr): self
    {
        $this->descr = $descr;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticleIdarticle(): Collection
    {
        return $this->articleIdarticle;
    }

    public function addArticleIdarticle(Article $articleIdarticle): self
    {
        if (!$this->articleIdarticle->contains($articleIdarticle)) {
            $this->articleIdarticle[] = $articleIdarticle;
        }

        return $this;
    }

    public function removeArticleIdarticle(Article $articleIdarticle): self
    {
        if ($this->articleIdarticle->contains($articleIdarticle)) {
            $this->articleIdarticle->removeElement($articleIdarticle);
        }

        return $this;
    }

    public function __toString():string
    {
        return $this->getTitre();
    }

}
