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
}
