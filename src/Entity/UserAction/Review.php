<?php
namespace UserAccountBundle\Entity\UserAction;

use RootBundle\Entity\Entity;
use RootBundle\Entity\Trait\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use UserAccountBundle\Entity\UserAuthorInterface;
use UserAccountBundle\Entity\UserAuthorTrait;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
abstract class Review extends Entity implements UserAuthorInterface{
    use TimestampsTrait;
    use UserAuthorTrait;
    /**
     * @var ?string
     * @ORM\Column(type="text", nullable=true)
     */
    #[Serializer\Groups(["review:read:collection", "review:update"])]
    protected $content = null;

    /**
     * @var ?int
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Serializer\Groups(["review:read:collection", "review:update"])]
    protected $rating;

    /**
     * Get the value of content
     *
     * @return  ?string
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  ?string  $content
     *
     * @return  self
     */ 
    public function setContent(?string $content)
    {
        $this->content = $content;

        return $this;
    }

    
    /**
     * Get the value of rating
     *
     * @return  ?int
     */ 
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set the value of rating
     *
     * @param  ?int  $rating
     *
     * @return  self
     * @throws \InvalidArgumentException
     */ 
    public function setRating(int $rating)
    {
        if($rating < 0 || $rating > 5)
            throw new \InvalidArgumentException("La note doit être entre 0 et 5");
        $this->rating = $rating;

        return $this;
    }
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function checkValues(){
        if($this->content == null && $this->rating == null)
            throw new \Exception("Les propriétés ".$this->content." et ".$this->rating." sont toutes les deux nulles");
    }
}