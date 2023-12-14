<?php
namespace UserAccountBundle\Controller;

use RootBundle\Controller\AbstractController;
use RootBundle\Library\FileUpload;
use RootBundle\Library\FileUploadException;
use RootBundle\Service\MailerService;
use UserAccountBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route("", name:"account.", priority: 100)]
class UserAccountController extends AbstractController{
    
    #[Route("/connexion", name:"login")]
    public function login(AuthenticationUtils $authUtils, Request $request, ParameterBagInterface $parameterBag){
        $referer = $request->headers->get("referer");
        if($referer != null){
            $explodeReferer = explode("/", $referer);
            if(end($explodeReferer) != "login"){
                $request->getSession()->set("login-referer", $referer);
            }else{
                $referer = $request->getSession()->get("login-referer", "/");
            }
        }
        $loginMessage = "";
        $action = strtolower($request->query->get("a", ""));
        foreach($parameterBag->get("user_account")["login_targets"] as $el){
            if($el["key"] == $action){
                $loginMessage = $el["message"];
                break;
            }
        }
        return $this->render("@UserAccount/login", [
            "app_name" => $parameterBag->get("app_name"),
            "default_message" => $parameterBag->get("user_account")["login_default_message"],
            "error" => $authUtils->getLastAuthenticationError(),
            "last_username" => $authUtils->getLastUsername(),
            "referer" => $referer,
            "loginMessage" => $loginMessage,
            "signUpUrl" => $parameterBag->get("user_account")["signup_url"]
        ]);
    }

    #[Route('/verification-email', name: "emailvalidation")]
    public function emailValidation(){
        return $this->render("@UserAccount/email-validation");
    }

    #[Route("/acces-refuse", name:"accessdenied")]
    public function accessDenied(){
        return $this->render("@Root/errors/error403");
    }
}