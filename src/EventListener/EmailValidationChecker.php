<?php
namespace UserAccountBundle\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UserAccountBundle\Entity\AdminInterface;
use UserAccountBundle\Entity\User;

/**
 * Check if the user email is validated if the 'absolute_email_validation' parameter is true
 */
#[AsEventListener]
class EmailValidationChecker{

    public function __construct(
        private Security $security, 
        private ParameterBagInterface $parameterBag,
        private UrlGeneratorInterface $urlGenerator
    ){}

    public function __invoke(RequestEvent $requestEvent){
        if(
            $requestEvent->getRequest()->attributes->get("_route") == "account.emailvalidation" ||
            str_contains($requestEvent->getRequest()->attributes->get("route"), 'profiler') ||
            str_starts_with($requestEvent->getRequest()->getPathInfo(), '/_wdt') ||
            str_starts_with($requestEvent->getRequest()->getPathInfo(), '/api')
        ) return;
        /** @var User */
        $user = $this->security->getUser();
        if($user && !($user instanceof AdminInterface) && !$user->isEmailValidated() && $this->parameterBag->get('user_account')['absolute_email_validation'])
            $requestEvent->setResponse(
                new RedirectResponse($this->urlGenerator->generate("account.emailvalidation"))
            );
    }
}