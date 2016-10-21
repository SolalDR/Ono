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
     * @var \Text
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var \Text
     *
     * @ORM\Column(name="firstname", type="text", nullable=false)
     */
    private $firstname;


    /**
     * @var \Date
     *
     * @ORM\Column(name="dtnaissance", type="date", nullable=true)
     * @Assert\Date(message="La date donnée est incorrecte !")
     */
    private $dtnaissance;


    /**
     * @var \Text
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Country")
    * @ORM\JoinColumn(nullable=true)
    */
    private $country;


    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Language")
    * @ORM\JoinColumn(nullable=true)
    */
    private $language;

    /**
    * @ORM\OneToMany(targetEntity="Ono\MapBundle\Entity\Response", mappedBy="user")
    * @ORM\JoinColumn(nullable=true)
    */
    private $responses;


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

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Add response
     *
     * @param \Ono\MapBundle\Entity\Response $response
     *
     * @return User
     */
    public function addResponse(\Ono\MapBundle\Entity\Response $response)
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * Remove response
     *
     * @param \Ono\MapBundle\Entity\Response $response
     */
    public function removeResponse(\Ono\MapBundle\Entity\Response $response)
    {
        $this->responses->removeElement($response);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->responses;
    }
}
