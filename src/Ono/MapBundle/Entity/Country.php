<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\CountryRepository")
 */
class Country
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
     * @ORM\Column(name="libCountry", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $libCountry;

    /**
     * @var string
     *
     * @ORM\Column(name="libCapital", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $libCapital;

    /**
     * @var int
     *
     * @ORM\Column(name="lat", type="integer")
     * @Assert\NotBlank()
     */
    private $lat;

    /**
     * @var int
     *
     * @ORM\Column(name="ln", type="integer")
     * @Assert\NotBlank()
     */
    private $ln;


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
     * Set libCountry
     *
     * @param string $libCountry
     *
     * @return Country
     */
    public function setLibCountry($libCountry)
    {
        $this->libCountry = $libCountry;

        return $this;
    }

    /**
     * Get libCountry
     *
     * @return string
     */
    public function getLibCountry()
    {
        return $this->libCountry;
    }

    /**
     * Set libCapital
     *
     * @param string $libCapital
     *
     * @return Country
     */
    public function setLibCapital($libCapital)
    {
        $this->libCapital = $libCapital;

        return $this;
    }

    /**
     * Get libCapital
     *
     * @return string
     */
    public function getLibCapital()
    {
        return $this->libCapital;
    }

    /**
     * Set lat
     *
     * @param integer $lat
     *
     * @return Country
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return int
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set ln
     *
     * @param integer $ln
     *
     * @return Country
     */
    public function setLn($ln)
    {
        $this->ln = $ln;

        return $this;
    }

    /**
     * Get ln
     *
     * @return int
     */
    public function getLn()
    {
        return $this->ln;
    }
}
