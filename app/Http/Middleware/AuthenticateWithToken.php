<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Doctrine\ORM\EntityManager;
use App\Entities\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticateWithToken
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
    
        if (!$token) {
            return response()->json(['message' => 'Token não fornecido'], 401);
        }
    
        $tokenRepository = $this->entityManager->getRepository(PersonalAccessToken::class);
        $accessToken = $tokenRepository->findOneBy(['token' => $token]);
    
        if (!$accessToken) {
            return response()->json(['message' => 'Token inválido'], 401);
        }
    

        Auth::loginUsingId($accessToken->getUserId());
    
        return $next($request);
    }
    
}