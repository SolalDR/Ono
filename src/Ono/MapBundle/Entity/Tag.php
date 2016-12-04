<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
}
