<?php
namespace UserAccountBundle\Controller\Api;

use UserAccountBundle\Entity\User;
use VporelBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use UserAccountBundle\Repository\UserRepositoryInterface;

/**
 * Get informations about a user even if they are not connected
 */
class UserController extends AbstractApiController{

    public function getByUserName(Request $request, UserRepositoryInterface $userRepository, string $userName)
    {
        $user = $userRepository->findOneBy(compact("userName"));
        if(!$user) return $this->error("No user found with the userName '$userName'");
        return $this->success($user);
    }

    public function searchUsers(Request $request, UserRepositoryInterface $userRepository)
    {
        $text = $request->query->get("text");
        if($text == null || $text == "") return $this->error("The search text should not be null or empty");
        $words = explode(" ", trim($text));
        $criteria = [];
        /** @var User */
        $user = $this->getUser();
        if($user) $criteria["id__neq"] = $user->getId();
        foreach($words as $word){
            if(!empty($word)){
                $criteria[] = [
                    ["firstName__like" => "%$word%"], ["userName__like" => "%$word%"]    //Name or userName
                ];
            }
        }
        $users = $userRepository->findBy($criteria);
        return $this->success($users, null, "simplified");
    }
}