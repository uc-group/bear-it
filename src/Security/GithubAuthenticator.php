<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private string $clientId,
        private string $clientSecret
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'auth_github';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $code = $request->query->get('code');
        if (!$code) {
            throw new CustomUserMessageAuthenticationException('No code provided');
        }

        $response = ($this->httpClient->request(
            'POST',
            'https://github.com/login/oauth/access_token',
            [
                'json' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'code' => $code
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ]
            ]
        ))->toArray();


        $accessToken = $response['access_token'] ?? null;
        if (!$accessToken) {
            throw new CustomUserMessageAuthenticationException('Invalid access token');
        }

        $userData = ($this->httpClient->request('GET', 'https://api.github.com/user', [
            'headers' => [
                'Authorization' => sprintf('token %s', $accessToken),
                'Accept' => 'application/json'
            ]
        ]))->toArray();

        $userId = $userData['id'] ?? null;
        if (!$userId) {
            throw new CustomUserMessageAuthenticationException('User not found');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'githubId' => $userId
        ]);

        if (!$user instanceof User) {
            $user = User::createGithubUser($userData);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/');
    }
}
