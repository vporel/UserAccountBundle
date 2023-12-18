<?php
namespace UserAccountBundle\Controller;

use VporelBundle\Controller\AbstractController;
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

        return $this->render("@UserAccount/login", [
            "error" => $authUtils->getLastAuthenticationError(),
            "last_username" => $authUtils->getLastUsername(),
            "referer" => $referer,
            "signUpUrl" => $parameterBag->get("user_account")["signup_url"]
        ]);
    }

    #[Route('/verification-email', name: "emailvalidation")]
    public function emailValidation(){
        return $this->render("@UserAccount/email-validation");
    }

    #[Route("/acces-refuse", name:"accessdenied")]
    public function accessDenied(){
        return $this->render("@Vporel/errors/error403");
    }
}