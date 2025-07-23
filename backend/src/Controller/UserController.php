<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/api/user', name: 'api_user', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function __invoke(): JsonResponse
{
    /** @var User $user */
    $user = $this->getUser();

    return $this->json([
        'email' => $user->getUserIdentifier(),
        'fullName' => $user->getFullName(),
    ]);
}
    
}
