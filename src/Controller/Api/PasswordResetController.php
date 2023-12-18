<?php
namespace UserAccountBundle\Controller\Api;

use VporelBundle\Service\MailerInterface;
use UserAccountBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use VporelBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use UserAccountBundle\Repository\UserRepositoryInterface;

#[Route("/api/account", name:"api.account.")]
class PasswordResetController extends AbstractApiController{

    public function __construct(private UserRepositoryInterface $userRepository){}

    #[Route("/send-password-reset-code", name:"sendpasswordresetcode", methods: ["POST"])]
    public function sendPasswordResetCode(Request $request, MailerInterface $mailer)
    {
        $email = $request->request->get("email");
        if(!$this->userRepository->findOneBy(["email" => $email])) return new JsonResponse($this->error("Aucun compte trouvé"));
        $code = rand(100000, 999990); // 6 digits
        $request->getSession()->set("password-reset-code", $code);
        $mailer->sendEmail(
            $email, 
            "Réinitialisation du mot de passe", 
            $this->renderView("@UserAccount/emails/password-reset.html.twig", compact("code"))
        );
        return new JsonResponse($this->success());
    }

    #[Route("/check-password-reset-code", name:"checkpasswordresetcode", methods: ["POST"])]
    public function checkPasswordReset(Request $request){
        $code = $request->request->get("code");
        return new JsonResponse(
            ($request->getSession()->get("password-reset-code") == $code) 
                ? $this->success() 
                : $this->error("code_incorrect")
        );
    }

    
    #[Route("/reset-password", name:"resetpassword", methods: ["POST"])]
    public function resetPassword(Request $request, EntityManagerInterface $em){
        $session = $request->getSession();
        $email = $request->request->get("email");
        $code = $request->request->get("code");
        $password = $request->request->get("password");
        $user = $this->userRepository->findOneBy(["email" => $email]);
        if($session->get("password-reset-code") == $code){  //Check the code again
            $session->remove("password-reset-code");
            $user->setPassword(User::hashPassword($password));
            $em->flush();
            return new JsonResponse($this->success());
        }else
            return new JsonResponse($this->error("code_incorrect")); 
    }
}