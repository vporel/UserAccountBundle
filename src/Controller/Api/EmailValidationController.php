<?php
namespace UserAccountBundle\Controller\Api;

use VporelBundle\Service\MailerInterface;
use UserAccountBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use VporelBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/account", name:"api.account.")]
class EmailValidationController extends AbstractApiController{

    #[Route("/send-email-validation-code", name:"sendemailvalidationcode", methods: ["POST"])]
    public function sendEmailValidationCode(Request $request, MailerInterface $mailer)
    {
        $code = rand(100000, 999990); // 6 digits
        $request->getSession()->set("email-validation-code", $code);
        /** @var User */
        $user = $this->getUser();
        $mailer->sendEmail(
            $user->getEmail(), 
            "Vérification de l'adresse email", 
            $this->renderView("@UserAccount/emails/email-validation.html.twig", compact("code"))
        );
        return new JsonResponse($this->success());
    }

    #[Route("/validate-email", name:"validateemail", methods: ["POST"])]
    public function validateEmail(Request $request, EntityManagerInterface $em){
        $session = $request->getSession();
        /** @var User */
        $user = $this->getUser();
        $code = $request->request->get("code");
        if($session->get("email-validation-code") == $code){
            $user->validateEmail();
            $em->flush();
            $session->remove("email-validation-code");
            return new JsonResponse($this->success());
        }else
            return new JsonResponse($this->error("code_incorrect")); 
    }

}