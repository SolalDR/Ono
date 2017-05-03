<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag
 *
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\TagRepository")
 */
class Tag
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
     * @ORM\Column(name="libTag", type="string", length=255, unique=true)
     */
    private $libTag;

    /**
     * @var string
     *
     * @ORM\Column(name="indefinition", type="text", nullable=true)
     * @Assert\Length(min=50, minMessage="La longueur de l'indéfinition doit être au minimum de 50 caractères !")
     */
    private $indefinition;

    /**
     * @var int
     *
     * @ORM\Column(name="usedCount", type="integer")
     */
    private $usedCount = 0;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Article", mappedBy="tags")
     */
    private $articles;

    /**
    * Constructor
    */
    public function __construct()
    {
      $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set libTag
     *
     * @param string $libTag
     *
     * @return Tag
     */
    public function setLibTag($libTag)
    {
        $this->libTag = $libTag;

        return $this;
    }

    /**
     * Get libTag
     *
     * @return string
     */
    public function getLibTag()
    {
        return $this->libTag;
    }

    /**
     * Set usedCount
     *
     * @param integer $usedCount
     *
     * @return Tag
     */
    public function setUsedCount($usedCount)
    {
        $this->usedCount = $usedCount;

        return $this;
    }

    /**
     * Get usedCount
     *
     * @return integer
     */
    public function getUsedCount()
    {
        return $this->usedCount;
    }

    public function incrementUsedCount()
    {
      $count = $this->getUsedCount();
      return $this->setUsedCount($count+1);
    }
    public function decrementUsedCount()
    {
      $count = $this->getUsedCount();
      return $this->setUsedCount($count+1);
    }

    /**
     * Set indefinition
     *
     * @param string $indefinition
     *
     * @return Tag
     */
    public function setIndefinition($indefinition)
    {
        $this->indefinition = $indefinition;

        return $this;
    }

    /**
     * Get indefinition
     *
     * @return string
     */
    public function getIndefinition()
    {
        return $this->indefinition;
    }

    /**
     * Add article
     *
     * @param \Ono\MapBundle\Entity\Article $article
     *
     * @return Tag
     */
    public function addArticle(\Ono\MapBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Ono\MapBundle\Entity\Article $article
     */
    public function removeArticle(\Ono\MapBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }
}
