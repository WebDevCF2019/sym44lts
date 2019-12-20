<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Article
 *
 * @ORM\Table(name="article", uniqueConstraints={@ORM\UniqueConstraint(name="slug_UNIQUE", columns={"slug"})}, indexes={@ORM\Index(name="fk_article_user_idx", columns={"user_iduser"})})
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @var int
     *
     * @ORM\Column(name="idarticle", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idarticle;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=150, nullable=false)
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=150, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="texte", type="text", length=65535, nullable=false)
     */
    private $texte;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="thedate", type="datetime", nullable=true)
     */
    private $thedate;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_iduser", referencedColumnName="iduser")
     * })
     */
    private $userIduser;


    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Categ", mappedBy="articleIdarticle")
     */
    private $categIdcateg;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categIdcateg = new \Doctrine\Common\Collections\ArrayCollection();
        // on va mettre la date actuelle par défault si on crée un nouvel article (new Article)
        $this->setThedate(new \Datetime());
    }

    public function getIdarticle(): ?int
    {
        return $this->idarticle;
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

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    public function getThedate(): ?\DateTimeInterface
    {
        return $this->thedate;
    }

    public function setThedate(?\DateTimeInterface $thedate): self
    {
        $this->thedate = $thedate;

        return $this;
    }

    public function getUserIduser(): ?User
    {
        return $this->userIduser;
    }

    public function setUserIduser(?User $userIduser): self
    {
        $this->userIduser = $userIduser;

        return $this;
    }

    /**
     * @return Collection|Categ[]
     */
    public function getCategIdcateg(): Collection
    {
        return $this->categIdcateg;
    }

    public function addCategIdcateg(Categ $categIdcateg): self
    {
        if (!$this->categIdcateg->contains($categIdcateg)) {
            $this->categIdcateg[] = $categIdcateg;
            $categIdcateg->addArticleIdarticle($this);
        }

        return $this;
    }

    public function removeCategIdcateg(Categ $categIdcateg): self
    {
        if ($this->categIdcateg->contains($categIdcateg)) {
            $this->categIdcateg->removeElement($categIdcateg);
            $categIdcateg->removeArticleIdarticle($this);
        }

        return $this;
    }

}
