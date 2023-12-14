<?php
namespace UserAccountBundle\Entity;
use Symfony\Component\Serializer\Annotation as Serializer;

/**
 * Implemented by the users that have interactions with socketIO through the node application
 */
interface NodeAppUserInterface{

    /**
     * The groups in which the user is
     *
     * @return array
     */
    #[Serializer\Groups(["user:read:collection"])]
    public function getRooms(): array;
}