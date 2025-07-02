<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateController
{
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(Request $request)
    {
        $userJson = $request->request->get('user');
        if (!$userJson) {
            return new JsonResponse(['error' => 'Missing user data'], 400);
        }

        $data = json_decode($userJson, true);
        #$userData = json_decode($userJson, true);
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON in user field'], 400);
        }

        $user = new User();
        $user->setName($data['name'] ?? null);
        $user->setEmail($data['email'] ?? null);

        $plainPassword = $data['password'] ?? '';
        if (!$plainPassword) {
            return new JsonResponse(['error' => 'Password is required'], 400);
        }
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        $photoFile = $request->files->get('photo');
        if ($photoFile) {

        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
