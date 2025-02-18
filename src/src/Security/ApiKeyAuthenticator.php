<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private const API_KEY_HEADER = 'X-API-KEY';

    public function __construct(
        private ApiKeyUserProvider $userProvider,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::API_KEY_HEADER);
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get(self::API_KEY_HEADER);
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('No API key provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiKey, function ($apiKey) {
                return $this->userProvider->loadUserByIdentifier($apiKey);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
