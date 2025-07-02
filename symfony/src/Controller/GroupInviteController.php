<?php
namespace App\Controller;

use App\Entity\Group;
use App\Entity\Invitation;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class GroupInviteController extends AbstractController
{
    #[Route('/api/groups/{id}/invite', name: 'group_invite', methods: ['POST'])]
    public function invite(
        int $id,
        Request $request,
        GroupRepository $groupRepo,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): JsonResponse {
        $group = $groupRepo->find($id);
        if (!$group) {
            return new JsonResponse(['error' => 'Group not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'Email is required'], 400);
        }

        $token = bin2hex(random_bytes(32));

        $acceptPath = "https://ton-app.com/invitations/respond?token=$token&response=yes";
        $declinePath = "https://ton-app.com/invitations/respond?token=$token&response=no";

        $invitation = new Invitation();
        $invitation->setEmail($email);
        $invitation->setPath($acceptPath);
        $invitation->setSentAt(new \DateTimeImmutable());
        $invitation->setGroup($group);

        // Persister
        $em->persist($invitation);
        $em->flush();

        $emailMessage = (new Email())
            ->from('camille.clappaz@laplateforme.io')
            ->to($email)
            ->subject('Invitation à rejoindre un groupe')
            ->html("
                <p>Vous avez été invité à rejoindre le groupe <strong>{$group->getName()}</strong>.</p>
                <p>
                    <a href='$acceptPath'>Oui, je veux rejoindre</a><br>
                    <a href='$declinePath'>Non, merci</a>
                </p>
            ");

       try {
           $mailer->send($emailMessage);
       } catch (\Exception $e) {
           return new JsonResponse(['error' => 'Erreur envoi mail : '.$e->getMessage()], 500);
       }

        return new JsonResponse(['message' => "Invitation envoyée à $email"]);
    }
}
