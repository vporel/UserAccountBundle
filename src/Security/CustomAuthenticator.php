<?php

namespace UserAccountBundle\Security;

use Symfony\Bundle\SecurityBundle\Security;
use UserAccountBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use UserAccountBundle\Repository\UserRepositoryInterface;

/**
 * @author Vivian NKOUANANG (https://github.com/vporel) <dev.vporel@gmail.com>
 */
class CustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ){}

    public function authenticate(Request $request): Passport
    {
        if($this->isJsonLogin($request))
            $request->request->add(json_decode($request->getContent(), true) ?? []);
        $userName = $request->request->get('userName', '');
        if(!$this->isJsonLogin($request)) {
            $badges = [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token'))
            ];
        }
        $request->getSession()->set(Security::LAST_USERNAME, $userName);
        return new Passport(
            new UserBadge($userName, function(string $userName){
                return $this->getUser($userName);
            }),
            new PasswordCredentials($request->request->get('password', '')),
            $badges ?? []
        );
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User */
        $user = $token->getUser();
        if($this->isJsonLogin($request))
            return new JsonResponse(["status" => 1, "statusCode" => 200]);
        $targetPath = $request->request->get("_target_path");
        if (
            $targetPath == null || $targetPath == "/" || 
            str_contains($targetPath, $this->getLoginUrl($request)) || 
            str_ends_with($targetPath, $this->getHomePagePath()) || str_ends_with($targetPath, $this->getHomePagePath()."/")
        ) {
            $targetPath = $user->getHomePagePath();
        }
        return new RedirectResponse($targetPath);    
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if($this->isJsonLogin($request)){
            $data = [
                'status' => 0,
                'statusCode' => JsonResponse::HTTP_UNAUTHORIZED,
                'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
            ];
            return new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED);
        }
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }
        return new RedirectResponse($request->getRequestUri());
    }

    public function supports(Request $request): bool
    {
        return ($request->isMethod('POST') && $request->getPathInfo() == $this->getLoginUrl($request));
    }

    private function isJsonLogin(Request $request){
        return strpos($request->getRequestFormat() ?? '', 'json') !== false || strpos($request->getContentTypeFormat() ?? '', 'json') !== false;
    }

    /**
     * Retrieve the user from the databse with the userName provided
     * @return User|null
     */
    protected function getUser(string $userName): User|null{
        return $this->userRepository->findOneBy(["userName" => $userName]);
    }
    
    protected function getHomePagePath(): string{
        return "/";
    }

    protected function getLoginUrl(Request $request): string
    {
        return "/connexion";
    }
}
