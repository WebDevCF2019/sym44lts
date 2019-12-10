<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="thelogin_UNIQUE", columns={"thelogin"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="iduser", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $iduser;

    /**
     * @var string
     *
     * @ORM\Column(name="thelogin", type="string", length=50, nullable=false)
     */
    private $thelogin;

    /**
     * @var string
     *
     * @ORM\Column(name="thename", type="string", length=200, nullable=false)
     */
    private $thename;

    /**
     * @var string
     *
     * @ORM\Column(name="thepwd", type="string", length=50, nullable=false)
     */
    private $thepwd;

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function getThelogin(): ?string
    {
        return $this->thelogin;
    }

    public function setThelogin(string $thelogin): self
    {
        $this->thelogin = $thelogin;

        return $this;
    }

    public function getThename(): ?string
    {
        return $this->thename;
    }

    public function setThename(string $thename): self
    {
        $this->thename = $thename;

        return $this;
    }

    public function getThepwd(): ?string
    {
        return $this->thepwd;
    }

    public function setThepwd(string $thepwd): self
    {
        $this->thepwd = $thepwd;

        return $this;
    }

    // création d'une méthode __tostring qui affichera les texte voulu si on essaye de lire l'objet comme si c'était une chaîne de caractère
    public function __toString():string
    {
        return $this->getThename()." ".$this->getThelogin();
    }


}
