<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Doctrine\ORM\EntityManager;
use App\Entities\User;
use App\Entities\PersonalAccessToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => $credentials['email']]);

        if (!$user || !Hash::check($credentials['password'], $user->getPassword())) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token = new PersonalAccessToken();
        $token->setToken(bin2hex(random_bytes(40)));
        $token->setUserId($user->getId());
        $token->setAbilities(['*']);
        $token->setCreatedAt(new \DateTime());

        $this->entityManager->persist($token);
        $this->entityManager->flush();

        // Retornar o token ao usuário
        return response()->json([
            'token' => $token->getToken(),
            'expires_at' => $token->getExpiresAt(),
        ]);
    }
}
