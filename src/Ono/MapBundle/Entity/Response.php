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
     * @Assert\Length(min=30)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtcreation", type="datetime")
     * @Assert\DateTime()
     */
    private $dtcreation;

    /**
     * @var \Date
     *
     * @ORM\Column(name="dtnaissance", type="date")
     * @Assert\Date()
     */
    private $dtnaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Assert\Length(min=2)
     */
    private $author;


    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = false;


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
}
