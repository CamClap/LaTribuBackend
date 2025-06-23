<?php

namespace App\Controller;

use App\Entity\Invitation;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class InvitationController extends AbstractController
{
    #[Route('/api/invite', name: 'api_invite_create', methods: ['POST'])]
    public function invite(
        Request $request,
        EntityManagerInterface $em,
        GroupRepository $groupRepo,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $groupId = $data['groupId'] ?? null;

        if (!$email || !$groupId) {
            return new JsonResponse(['error' => 'Email et groupId requis'], 400);
        }

        $user = $this->getUser();
        $group = $groupRepo->find($groupId);

        if (!$group || $group->getCreator() !== $user) {
            return new JsonResponse(['error' => 'Non autorisé'], 403);
        }

        $path = bin2hex(random_bytes(16)); // ex: 32 caractères uniques

        $invitation = new Invitation();
        $invitation->setEmail($email);
        $invitation->setPath($path);
        $invitation->setSentAt(new \DateTimeImmutable());
        $invitation->setGroup($group);

        $em->persist($invitation);
        $em->flush();

        $yesUrl = "https://tonsite.com/register?invitation=$path";
        $noUrl = "https://tonsite.com/invite/refused";

        $emailMessage = (new TemplatedEmail())
            ->to($email)
            ->subject("Invitation à rejoindre le groupe {$group->getName()}")
            ->htmlTemplate('emails/invitation.html.twig')
            ->context([
                'yesUrl' => $yesUrl,
                'noUrl' => $noUrl,
                'groupName' => $group->getName(),
            ]);

        $mailer->send($emailMessage);

        return new JsonResponse(['success' => true]);
    }
}
