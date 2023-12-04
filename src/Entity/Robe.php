<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Robe
 *
 * @ORM\Table(name="robe")
 * @ORM\Entity(repositoryClass="App\Repository\RobeRepository")
 */
class Robe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=40, nullable=false)
     */
    private $libelle;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
    public function __toString()
    {
        return $this->libelle;
    }
}
