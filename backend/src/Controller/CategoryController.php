<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security; // ✅ Correct for Symfony 7

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'create_category', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || empty(trim($data['name']))) {
            return new JsonResponse(['error' => 'Category name is required'], 400);
        }

        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $category = new Category();
        $category->setName(trim($data['name']));
        $category->setUser($user);

        $em->persist($category);
        $em->flush();

        return $this->json([
            'id' => $category->getId(),
            'name' => $category->getName(),
        ], 201);
    }
    #[Route('', name: 'list_categories', methods: ['GET'])]
    public function list(EntityManagerInterface $em, Security $security): JsonResponse
    {
        $user = $security->getUser();
        $categories = $em->getRepository(Category::class)->findBy(['user' => $user]);

        $result = array_map(fn($cat) => [
            'id' => $cat->getId(),
            'name' => $cat->getName(),
        ], $categories);

        return $this->json($result);
    }
}
