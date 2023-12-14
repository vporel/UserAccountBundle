<?php
namespace UserAccountBundle\Entity;

interface UserAuthorInterface{

    public function getAuthor(): User;

    /**
     * @return void
     */
    public function setAuthor(User $author);
}