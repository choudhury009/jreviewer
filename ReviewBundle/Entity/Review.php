<?php

namespace Reviewer\ReviewBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Review
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Reviewer\ReviewBundle\Entity\ReviewRepository")
 * @ExclusionPolicy("all")
 */
class Review
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="text")
     * @Expose
     */
    private $review;

    /**
     * @var \Reviewer\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Reviewer\UserBundle\Entity\User",inversedBy="reviews")
     * @ORM\JoinColumn(name="author", referencedColumnName="id")
     * @Expose
     */
    private $author;

    /**
     * @var \Reviewer\ReviewBundle\Entity\Book
     * @ORM\ManyToOne(targetEntity="Reviewer\ReviewBundle\Entity\Book")
     * @ORM\JoinColumn(name="book", referencedColumnName="id")
     */
    private $book;

    /**
     * @var integer
     * @ORM\Column(name="votes", type="integer")
     * @Expose
     */
    private $votes;

    /**
     * @var string
     * @ORM\Column(name="up_voters", type="string", nullable=true)
     */
    private $upVoters;

    /**
     * @var string
     * @ORM\Column(name="down_voters", type="string", nullable=true)
     */
    private $downVoters;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param \Reviewer\UserBundle\Entity\User $author
     *
     * @return Review
     */
    public function setAuthor(\Reviewer\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Reviewer\UserBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set review
     *
     * @param string $review
     *
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set book
     *
     * @param \Reviewer\ReviewBundle\Entity\Book $book
     *
     * @return Review
     */
    public function setBook(\Reviewer\ReviewBundle\Entity\Book $book = null)
    {
        $this->book = $book;

        return $this;
    }

    /**
     * Get book
     *
     * @return \Reviewer\ReviewBundle\Entity\Book
     */
    public function getBook()
    {
        return $this->book;
    }

    /**
     * Set votes
     *
     * @param integer $votes
     *
     * @return Review
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * Get votes
     *
     * @return integer
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set upVoters
     *
     * @param string $upVoters
     *
     * @return Review
     */
    public function setUpVoters($upVoters)
    {
        $this->upVoters = $upVoters;

        return $this;
    }

    /**
     * Get upVoters
     *
     * @return string
     */
    public function getUpVoters()
    {
        return $this->upVoters;
    }

    /**
     * Set downVoters
     *
     * @param string $downVoters
     *
     * @return Review
     */
    public function setDownVoters($downVoters)
    {
        $this->downVoters = $downVoters;

        return $this;
    }

    /**
     * Get downVoters
     *
     * @return string
     */
    public function getDownVoters()
    {
        return $this->downVoters;
    }
}
