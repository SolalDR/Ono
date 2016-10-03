<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\QuestionRepository")
 */
class Question
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
     * @ORM\Column(name="libQuestion", type="string", length=255, unique=true)
     */
    private $libQuestion;

    /**
    * @ORM\OneToMany(targetEntity="Ono\MapBundle\Entity\Response", mappedBy="question")
    */
    private $responses;

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
     * Set libQuestion
     *
     * @param string $libQuestion
     *
     * @return Question
     */
    public function setLibQuestion($libQuestion)
    {
        $this->libQuestion = $libQuestion;

        return $this;
    }

    /**
     * Get libQuestion
     *
     * @return string
     */
    public function getLibQuestion()
    {
        return $this->libQuestion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->responses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add response
     *
     * @param \Ono\MapBundle\Entity\Response $response
     *
     * @return Question
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
