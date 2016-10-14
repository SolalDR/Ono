<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Language
 *
 * @ORM\Table(name="language")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\LanguageRepository")
 */
class Language
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
     * @ORM\Column(name="cdLanguage", type="string", length=255, unique=true)
     * @Assert\Length(max=4, maxMessage="Le code langue ne doit pas dépasser 4 caractères !")
     */
    private $cdLanguage;

    /**
     * @var string
     *
     * @ORM\Column(name="libLanguageFr", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide !")
     */
    private $libLanguageFr;

    /**
     * @var string
     *
     * @ORM\Column(name="libLanguageEn", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le champ ne doit pas être vide !")
     */
    private $libLanguageEn;


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
     * Set cdLanguage
     *
     * @param string $cdLanguage
     *
     * @return Language
     */
    public function setCdLanguage($cdLanguage)
    {
        $this->cdLanguage = $cdLanguage;

        return $this;
    }

    /**
     * Get cdLanguage
     *
     * @return string
     */
    public function getCdLanguage()
    {
        return $this->cdLanguage;
    }

    /**
     * Set libLanguageFr
     *
     * @param string $libLanguageFr
     *
     * @return Language
     */
    public function setLibLanguageFr($libLanguageFr)
    {
        $this->libLanguageFr = $libLanguageFr;

        return $this;
    }

    /**
     * Get libLanguageFr
     *
     * @return string
     */
    public function getLibLanguageFr()
    {
        return $this->libLanguageFr;
    }

    /**
     * Set libLanguageEn
     *
     * @param string $libLanguageEn
     *
     * @return Language
     */
    public function setLibLanguageEn($libLanguageEn)
    {
        $this->libLanguageEn = $libLanguageEn;

        return $this;
    }

    /**
     * Get libLanguageEn
     *
     * @return string
     */
    public function getLibLanguageEn()
    {
        return $this->libLanguageEn;
    }
}
