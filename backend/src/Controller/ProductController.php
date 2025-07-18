<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\SecurityBundle\Security; // ✅ Symfony 7-compatible Security

#[Route('/api/products', name: 'api_product_')]
class ProductController extends AbstractController
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
        $product = new Product();

        // Basic fields
        $product->setName($request->get('name'));
        $product->setSku($request->get('sku'));
        $product->setCostPrice((float)$request->get('costPrice'));
        $product->setValuePrice((float)$request->get('valuePrice'));

        $soldPrice = $request->get('soldPrice');
        $product->setSoldPrice($soldPrice !== null ? (float)$soldPrice : null);

        $product->setStock((int)$request->get('stock'));
        $product->setComment($request->get('comment'));
        $product->setDescription($request->get('description'));

        // Dates
        $purchaseDate = $request->get('purchaseDate');
        $soldDate = $request->get('soldDate');

        try {
            $product->setPurchaseDate(new \DateTime($purchaseDate));
            $product->setSoldDate($soldDate ? new \DateTime($soldDate) : null);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], 400);
        }

        // Category
        $categoryId = $request->get('categoryId');
        $category = $this->em->getRepository(Category::class)->find($categoryId);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], 400);
        }

        $product->setCategory($category);

        // User
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $product->setUser($user);

        // Optional photo
        /** @var UploadedFile|null $uploadedFile */
        $uploadedFile = $request->files->get('photo');
        if ($uploadedFile) {
            $filename = uniqid() . '.' . $uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('uploads_directory'), $filename);
            $product->setPhoto($filename);
        }

        $this->em->persist($product);
        $this->em->flush();

        return new JsonResponse([
            'id' => $product->getId(),
            'message' => 'Product created successfully',
        ], Response::HTTP_CREATED);
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ProductRepository $repo): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $products = $repo->findBy(['user' => $user]);

        return $this->json($products, 200, [], ['groups' => ['product:read']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
public function delete(Product $product): JsonResponse
{
    $user = $this->security->getUser();
    if (!$user || $product->getUser() !== $user) {
        return new JsonResponse(['error' => 'Unauthorized'], 403);
    }

    $this->em->remove($product);
    $this->em->flush();

    return new JsonResponse(['message' => 'Product deleted successfully']);
}

}
