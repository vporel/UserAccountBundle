<?php
namespace UserAccountBundle\Controller\Api;

use VporelBundle\Library\FileUpload;
use VporelBundle\Library\FileUploadException;
use UserAccountBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use VporelBundle\Controller\AbstractApiController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/account", name:"api.account.")]
class UserAccountController extends AbstractApiController{

    #[Route("/change-email", name:"changeemail", methods: ["POST"])]
    public function changeEmail(Request $request, EntityManagerInterface $em){
        /** @var User */
        $user = $this->getUser();
        $email = $request->request->get("email");
        $user->setEmail($email);
        $invalidEmail = false;
        $errors = $this->validate($user);
        if(count($errors) > 0){
            foreach($errors as $err)
                if($err->getPropertyPath() == "email") $invalidEmail = true;
        }
        if($invalidEmail) return new JsonResponse("Adresse email invalide");
        $em->flush();
        return new JsonResponse($this->success());
    }

    #[Route("/change-password", name:"changepassword", methods: ["POST"])]
    public function changePassword(Request $request, EntityManagerInterface $em){
        /** @var User */
        $user = $this->getUser();
        $currentPassword = $request->request->get("currentPassword");
        $newPassword = $request->request->get("newPassword");
        if(!$user->testPassword($currentPassword)) return new JsonResponse($this->error("current_password_incorrect"));
        if(strlen($newPassword) < 6) return new JsonResponse($this->error("new_password_length"));
        $user->setPassword(User::hashPassword($newPassword));
        $em->flush();
        return new JsonResponse($this->success());
    }

    #[Route("/change-profile-photo", name:"changeprofilephoto", methods: ["POST"])]
    public function changeProfilePhoto(Request $request, EntityManagerInterface $em, ParameterBagInterface $parameterBag){
        /** @var User */
        $user = $this->getUser();
        $file = $request->files->get("file");
        if($file){
            try{
                $fileName = FileUpload::uploadFile($file, $parameterBag->get("public_dir") . User::AVATARS_FOLDER, [
                    "extensions" => User::AVATARS_EXTENSIONS,
                    "nameFormatFunction" => function($originalFileName, $extension) use($user){
                        return $user->getId() . $extension;
                    }
                ]);
                $user->setAvatarFile($fileName);
                $em->flush();
                return new JsonResponse($this->success([
                    "avatarFile" => $user->getAvatarFile(),
                    "avatarUrl" => $user->getAvatarUrl()
                ]));
            }catch(FileUploadException $e){
                return new JsonResponse($this->error($e->getMessage()));
            }
        }else 
            return new JsonResponse($this->error("no_file"));
    }

}