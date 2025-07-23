<?php

namespace App\Controller;

use App\Entity\Revenue;
use App\Repository\RevenueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;

#[Route('/api/revenue', name: 'api_revenue_')]
class RevenueController extends AbstractController
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

    $revenue = new Revenue();
    $revenue->setUser($user);
    $revenue->setDescription($data['description'] ?? '');
    $revenue->setAmount((float) ($data['amount'] ?? 0));
    $revenue->setCategory($data['category'] ?? '');
    $revenue->setPaymentMethod($data['paymentMethod'] ?? null);
    $revenue->setCustomer($data['customer'] ?? null);
    $revenue->setInvoiceNumber($data['invoiceNumber'] ?? null);
    $revenue->setNotes($data['notes'] ?? null);

    try {
        $date = new \DateTime($data['date'] ?? 'now');
        $revenue->setDate($date);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Invalid date format'], 400);
    }

    $this->em->persist($revenue);
    $this->em->flush();

    return new JsonResponse([
        'id' => $revenue->getId(),
        'message' => 'Revenue created successfully'
    ], Response::HTTP_CREATED);
}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(RevenueRepository $repo): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $revenues = $repo->findBy(['user' => $user]);

        return $this->json($revenues, 200, [], ['groups' => ['revenue:read']]);
    }

        #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
public function delete(Revenue $revenue): JsonResponse
{
    $user = $this->security->getUser();
    if (!$user || $revenue->getUser() !== $user) {
        return new JsonResponse(['error' => 'Unauthorized'], 403);
    }

    $this->em->remove($revenue);
    $this->em->flush();

    return new JsonResponse(['message' => 'Revenue deleted successfully']);
}
}
