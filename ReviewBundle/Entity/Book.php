<?php

namespace Reviewer\ReviewBundle\Entity;

use Sonata\MediaBundle\Model\MediaInterface as MediaInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Book
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="Reviewer\ReviewBundle\Entity\BookRepository")
 * @ExclusionPolicy("all")
 */
class Book
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Expose
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     * @Expose
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="text")
     * @Expose
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="text")
     * @Expose
     */
    private $review;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     * @var \Reviewer\UserBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Reviewer\UserBundle\Entity\User",inversedBy="books")
     * @ORM\JoinColumn(name="uploader", referencedColumnName="id")
     */
    private $uploader;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="shelf", type="string", length=255, nullable=true)
     */
    private $shelf;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="LAZY")
     */
    protected $media;

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
     * Set title
     *
     * @param string $title
     *
     * @return Book
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
     * Set author
     *
     * @param string $author
     *
     * @return Book
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
     * Set summary
     *
     * @param string $summary
     *
     * @return Book
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set reviewer
     *
     * @param string $reviewer
     *
     * @return Book
     */
    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    /**
     * Get reviewer
     *
     * @return string
     */
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * Set review
     *
     * @param string $review
     *
     * @return Book
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
     * Set uploader
     *
     * @param \Reviewer\UserBundle\Entity\User $uploader
     *
     * @return Book
     */
    public function setUploader(\Reviewer\UserBundle\Entity\User $uploader = null)
    {
        $this->uploader = $uploader;

        return $this;
    }

    /**
     * Get uploader
     *
     * @return \Reviewer\UserBundle\Entity\User
     */
    public function getUploader()
    {
        return $this->uploader;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     *
     * @return Book
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set shelf
     *
     * @param string $shelf
     *
     * @return Book
     */
    public function setShelf($shelf)
    {
        $this->shelf = $shelf;

        return $this;
    }

    /**
     * Get shelf
     *
     * @return string
     */
    public function getShelf()
    {
        return $this->shelf;
    }

    /**
     * @param MediaInterface $media
     */
    public function setMedia(MediaInterface $media)
    {
        $this->media = $media;
    }

    /**
     * @return MediaInterface
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Book
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
