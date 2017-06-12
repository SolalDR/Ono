<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Indefinition
 *
 * @ORM\Table(name="indefinition")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\IndefinitionRepository")
 */
class Indefinition
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
     * @Assert\Length(min=20, minMessage="La longueur de l'indéfinition doit être d'au moins 20 caractères.")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=50, maxMessage="La longueur du nom de l'auteur ne doit pâs dépasser 50 caractères.")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Ono\MapBundle\Entity\Tag", inversedBy="indefinitions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tag;

    /**
     * @ORM\ManyToOne(targetEntity="Ono\UserBundle\Entity\User", inversedBy="indefinitions")
     * @ORM\JoinColumn(nullable=true)
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
     * @return Indefinition
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
     * Set tag
     *
     * @param \Ono\MapBundle\Entity\Tag $tag
     *
     * @return Indefinition
     */
    public function setTag(\Ono\MapBundle\Entity\Tag $tag)
    {
        $this->tag = $tag;
        $tag->addIndefinition($this);

        return $this;
    }

    /**
     * Get tag
     *
     * @return \Ono\MapBundle\Entity\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set user
     *
     * @param \Ono\UserBundle\Entity\User $user
     *
     * @return Indefinition
     */
    public function setUser(\Ono\UserBundle\Entity\User $user)
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
      $this->author = ($user->getFirstname().' '.$user->getName());
      return true;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Indefinition
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
}
