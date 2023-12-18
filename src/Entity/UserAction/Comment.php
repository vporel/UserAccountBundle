<?php
namespace UserAccountBundle\Entity\UserAction;

use VporelBundle\Entity\Entity;
use VporelBundle\Entity\Trait\TimestampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use UserAccountBundle\Entity\UserAuthorInterface;
use UserAccountBundle\Entity\UserAuthorTrait;

/**
 * @ORM\MappedSuperclass
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
abstract class Comment extends Entity implements UserAuthorInterface{
    use TimestampsTrait;
    use UserAuthorTrait;

    public const DEFAULT_NORMALIZATION_CONTEXT_GROUPS = ["default", "comment:read:collection", "user:read:simplified"];

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank(message:"Le commentaire ne peut être vide")]
    #[Serializer\Groups(["comment:read:collection", "comment:update"])]
    protected $content;

    /**
     * Get the value of content
     *
     * @return  string
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @param  string  $content
     *
     * @return  self
     */ 
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }
}