<?
namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/api/auth/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank(), new Assert\Length(min: 6)],
            'name' => [new Assert\NotBlank(), new Assert\Length(min: 2)],
        ]);

        $errors = $validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return $this->json(['message' => implode(', ', $messages)], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'Email already in use.'], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setFullName($data['name'] ?? null);
        $user->setEmail($data['email']);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
    }
}
