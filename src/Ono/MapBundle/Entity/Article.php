<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Article
 *
 * @ORM\Table(name="article")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\ArticleRepository")
 */
class Article
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtcreation", type="datetime")
     * @Assert\DateTime(message="Le Datetime donné est incorrect !")
     */
    private $dtcreation;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbLike", type="integer")
     * @Assert\Type(type="integer", message="La valeur donnée n'est pas un integer !")
     */
    private $nbLikes = 0;

    /**
    * @ORM\ManyToMany(targetEntity="Ono\MapBundle\Entity\Theme", cascade={"persist"})
    * @Assert\Valid()
    * @Assert\NotBlank(message="Le champ ne doit pas être vide !")
    */
    private $themes;

    /**
    * @ORM\ManyToMany(targetEntity="Ono\MapBundle\Entity\Tag", cascade={"persist"}, inversedBy="articles")
    * @Assert\Valid()
    * @Assert\NotBlank(message="Le champ ne doit pas être vide !")
    */
    private $tags;

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
    * Many Articles have Many Resources
    * @ORM\ManyToMany(targetEntity="Ono\MapBundle\Entity\Resource", cascade={"persist", "remove"}, orphanRemoval=true)
    * @ORM\JoinTable(name="articles_resources",
    *     joinColumns={@ORM\JoinColumn(name="article_id", referencedColumnName="id")},
    *     inverseJoinColumns={@ORM\JoinColumn(name="resource_id", referencedColumnName="id", unique=true)}
    *     )
    */
    private $resources;

    /**
     * Constructor
     */
    public function __construct()
    {
      $this->themes = new \Doctrine\Common\Collections\ArrayCollection();
      $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     *
     * @return Article
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Article
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
     * Set content
     *
     * @param string $content
     *
     * @return Article
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
     * Set published
     *
     * @param boolean $published
     *
     * @return Article
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
     * Set dtcreation
     *
     * @param \DateTime $dtcreation
     *
     * @return Article
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
     * Set nbLikes
     *
     * @param integer $nbLikes
     *
     * @return Article
     */
    public function setNbLikes($nbLikes)
    {
        $this->nbLikes = $nbLikes;

        return $this;
    }

    /**
     * Get nbLikes
     *
     * @return integer
     */
    public function getNbLikes()
    {
        return $this->nbLikes;
    }

    public function incrementLikes(){
      $actual = $this->getNbLikes();
      $new = $actual+1;
      $this->setNbLikes($new);
      return $new;
    }

    public function decrementLikes(){
      $actual = $this->getNbLikes();
      $new = $actual-1;
      $this->setNbLikes($new);
      return $new;
    }

    /**
     * Add theme
     *
     * @param \Ono\MapBundle\Entity\Theme $theme
     *
     * @return Article
     */
    public function addTheme(\Ono\MapBundle\Entity\Theme $theme)
    {
        $this->themes[] = $theme;

        return $this;
    }

    /**
     * Remove theme
     *
     * @param \Ono\MapBundle\Entity\Theme $theme
     */
    public function removeTheme(\Ono\MapBundle\Entity\Theme $theme)
    {
        $this->themes->removeElement($theme);
    }

    /**
     * Get themes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * Set country
     *
     * @param \Ono\MapBundle\Entity\Country $country
     *
     * @return Article
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
     * Set language
     *
     * @param \Ono\MapBundle\Entity\Language $language
     *
     * @return Article
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
     * Set user
     *
     * @param \Ono\UserBundle\Entity\User $user
     *
     * @return Article
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

    /**
     * Add tag
     *
     * @param \Ono\MapBundle\Entity\Tag $tag
     *
     * @return Article
     */
    public function addTag(\Ono\MapBundle\Entity\Tag $tag)
    {
      $this->tags[] = $tag;
      return $this;
    }

    /**
     * Remove tag
     *
     * @param \Ono\MapBundle\Entity\Tag $tag
     */
    public function removeTag(\Ono\MapBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Remove tag
     *
     * @param \Ono\MapBundle\Entity\Tag $tag
     */
    public function removeTags()
    {
        $this->tags = null;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add resource
     *
     * @param \Ono\MapBundle\Entity\Resource $resource
     *
     * @return Article
     */
    public function addResource(\Ono\MapBundle\Entity\Resource $resource)
    {
        $this->resources[] = $resource;

        return $this;
    }


    public function temporyDeleteResource(){
      $this->resources = [];
    }

    public function temporyDeleteUser(){
      $this->user = null;
    }

    public function temporyDeleteTagsIndefs() {
      $nbTags = count($this->tags);
      for ($i = 0 ; $i < $nbTags ; $i++) {
        $this->tags[$i] = [
          "id" => $this->tags[$i]->getId(),
          "libTag" => $this->tags[$i]->getLibTag()
        ];
      }
    }

    /**
     * Remove resource
     *
     * @param \Ono\MapBundle\Entity\Resource $resource
     */
    public function removeResource(\Ono\MapBundle\Entity\Resource $resource)
    {
        $this->resources->removeElement($resource);
    }

    /**
     * Get resources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResources()
    {
        return $this->resources;
    }
}
