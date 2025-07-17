<?php

namespace App\Controller;

use App\Entity\Expenses;
use App\Repository\ExpensesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/expenses', name: 'api_expenses_')]
class ExpensesController extends AbstractController
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
{
    $user = $this->security->getUser();
    if (!$user) {
        return new JsonResponse(['error' => 'Unauthorized'], 401);
    }

    $data = $request->toArray(); // ✅ Parse JSON body

    $expenses = new Expenses();
    $expenses->setUser($user);
    $expenses->setDescription($data['description'] ?? '');
    $expenses->setAmount((float) ($data['amount'] ?? 0));
    $expenses->setCategory($data['category'] ?? '');
    $expenses->setPaymentMethod($data['paymentMethod'] ?? null);
    $expenses->setVendor($data['vendor'] ?? null);
    $expenses->setReceiptNumber($data['receiptNumber'] ?? null);
    $expenses->setNotes($data['notes'] ?? null);

    try {
        $date = new \DateTime($data['date'] ?? 'now');
        $expenses->setDate($date);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Invalid date format'], 400);
    }

    $this->em->persist($expenses);
    $this->em->flush();

    return new JsonResponse([
        'id' => $expenses->getId(),
        'message' => 'Expense created successfully'
    ], Response::HTTP_CREATED);
}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ExpensesRepository $repo): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $revenues = $repo->findBy(['user' => $user]);

        return $this->json($revenues, 200, [], ['groups' => ['expenses:read']]);
    }
}
