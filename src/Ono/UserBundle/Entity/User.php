<?php

namespace Ono\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Ono\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var \Date
     *
     * @ORM\Column(name="dtnaissance", type="date")
     * @Assert\Date(message="La date donnée est incorrecte !")
     */
    private $dtnaissance;


    /**
     * @var \Text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\Date(message="La date donnée est incorrecte !")
     */
    private $description;


    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Country")
    * @ORM\JoinColumn(nullable=false)
    */
    private $country;


    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Language")
    * @ORM\JoinColumn(nullable=false)
    */
    private $language;




    /**
     * Set country
     *
     * @param \Ono\MapBundle\Entity\Country $country
     *
     * @return User
     */
    public function setCountry(\Ono\MapBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Ono\MapBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set dtnaissance
     *
     * @param \DateTime $dtnaissance
     *
     * @return User
     */
    public function setDtnaissance($dtnaissance)
    {
        $this->dtnaissance = $dtnaissance;

        return $this;
    }

    /**
     * Get dtnaissance
     *
     * @return \DateTime
     */
    public function getDtnaissance()
    {
        return $this->dtnaissance;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return User
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
     * Set language
     *
     * @param \Ono\MapBundle\Entity\Language $language
     *
     * @return User
     */
    public function setLanguage(\Ono\MapBundle\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \Ono\MapBundle\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
