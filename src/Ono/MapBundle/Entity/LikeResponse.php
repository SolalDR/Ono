<?php

namespace Ono\MapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * LikeResponse
 *
 * @ORM\Table(name="like_response")
 * @ORM\Entity(repositoryClass="Ono\MapBundle\Repository\LikeResponseRepository")
 */
class LikeResponse
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
    * @ORM\OneToOne(targetEntity="Ono\MapBundle\Entity\Response")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\Valid()
    */
    private $response;


    /**
    * @ORM\OneToOne(targetEntity="Ono\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
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
     * Set response
     *
     * @param \Ono\MapBundle\Entity\Response $response
     *
     * @return LikeResponse
     */
    public function setResponse(\Ono\MapBundle\Entity\Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return \Ono\MapBundle\Entity\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set user
     *
     * @param \Ono\UserBundle\Entity\User $user
     *
     * @return LikeResponse
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
}
