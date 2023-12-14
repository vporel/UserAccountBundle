<?php
namespace UserAccountBundle\Entity;

use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * For the entity that are actions made by a user
 */
trait UserAuthorTrait{
    
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="UserAccountBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    #[Serializer\Groups(["default"])]
    protected $author;

    public function getAuthor(): User
    {
        return $this->author;
    }

    
    /**
     * @return void
     */
    public function setAuthor(User $author)
    {
        $this->author = $author;
    }
}