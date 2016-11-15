<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Response
 *
 * @ORM\Table(name="response")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\ResponseRepository")
 */
class Response
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
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     * @Assert\Length(min=30, minMessage="La longueur de la réponse doit être au minimum de 30 caractères !")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtcreation", type="datetime")
     * @Assert\DateTime(message="Le Datetime donné est incorrect !")
     */
    private $dtcreation;

    /**
     * @var \Date
     *
     * @ORM\Column(name="dtnaissance", type="date")
     * @Assert\Date(message="La date donnée est incorrecte !")
     */
    private $dtnaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2, minMessage="Le nom doit être au minimum de 2 caractères !")
     */
    private $author;


    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     * @Assert\Type(type="bool", message="La valeur donnée n'est pas un booléen !")
     */
    private $published = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="nbLikes", type="boolean")
     * @Assert\Type(type="integer", message="La valeur donnée n'est pas un nombre !")
     */
    private $nbLikes = 0;

    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Question", inversedBy="responses")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\Valid()
    */
    private $question;

    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Country")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\Valid()
    */
    private $country;

    /**
    * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Language")
    * @ORM\JoinColumn(nullable=true)
    * @Assert\Valid()
    */
    private $language;

    /**
    * @ORM\ManyToOne(targetEntity="Ono\UserBundle\Entity\User", inversedBy="responses")
    * @ORM\JoinColumn(nullable=true)
    * @Assert\Valid()
    */
    private $user;


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
     * Set content
     *
     * @param string $content
     *
     * @return Response
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dtcreation
     *
     * @param \DateTime $dtcreation
     *
     * @return Response
     */
    public function setDtcreation($dtcreation)
    {
        $this->dtcreation = $dtcreation;

        return $this;
    }

    /**
     * Get dtcreation
     *
     * @return \DateTime
     */
    public function getDtcreation()
    {
        return $this->dtcreation;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Response
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return Response
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set question
     *
     * @param \Ono\MapBundle\Entity\Question $question
     *
     * @return Response
     */
    public function setQuestion(\Ono\MapBundle\Entity\Question $question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Ono\MapBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set country
     *
     * @param \Ono\MapBundle\Entity\Country $country
     *
     * @return Response
     */
    public function setCountry(\Ono\MapBundle\Entity\Country $country)
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
     * @return Response
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
     * Set user
     *
     * @param \Ono\UserBundle\Entity\User $user
     *
     * @return Response
     */
    public function setUser(\Ono\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \Ono\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function updateUser(\Ono\UserBundle\Entity\User $user = null){
      if($user){
        $this->user = $user;
      } elseif($this->user) {
        $user = $this->user;
      } else {
        return false;
      }
      $this->country = $user->getCountry();
      $this->language = $user->getLanguage();
      $this->dtnaissance = $user->getDtnaissance();
      $this->language = $user->getLanguage();
      $this->author = $user->getFirstname().' '.$user->getName();
      return true;
    }


    /**
     * Set language
     *
     * @param \Ono\MapBundle\Entity\Language $language
     *
     * @return Response
     */
    public function setLanguage(\Ono\MapBundle\Entity\Language $language = null)
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
     * Set nbLikes
     *
     * @param boolean $nbLikes
     *
     * @return Response
     */
    public function setNbLikes($nbLikes)
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    /**
     * Get nbLikes
     *
     * @return boolean
     */
    public function getNbLikes()
    {
        return $this->nbLikes;
    }

    public function incrementLikes(){
      $nbActual = $this->getNbLikes();
      $new = $nbActual+1;
      $this->setNbLikes($new);
    }

    public function decrementLikes(){
      $nbActual = $this->getNbLikes();
      $new = $nbActual-1;
      $this->setNbLikes($new);
    }

}
