<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\ThemeRepository")
 */
class Theme
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
     * @ORM\Column(name="libTheme", type="string", length=255, unique=true)
     */
    private $libTheme;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    //Utile notamment pour les picto associé
    /**
     * @var string
     *
     * @ORM\Column(name="cdTheme", type="string", length=255, unique=false, nullable=true)
     */
    private $cdTheme;

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
     * Set libTheme
     *
     * @param string $libTheme
     *
     * @return Theme
     */
    public function setLibTheme($libTheme)
    {
        $this->libTheme = $libTheme;

        return $this;
    }

    /**
     * Get libTheme
     *
     * @return string
     */
    public function getLibTheme()
    {
        return $this->libTheme;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Theme
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set cdTheme
     *
     * @param string $cdTheme
     *
     * @return Theme
     */
    public function setCdTheme($cdTheme)
    {
        $this->cdTheme = $cdTheme;

        return $this;
    }

    /**
     * Get cdTheme
     *
     * @return string
     */
    public function getCdTheme()
    {
        return $this->cdTheme;
    }
}
