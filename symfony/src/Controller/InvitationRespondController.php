<?php

namespace App\Controller;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationRespondController extends AbstractController
{
    #[Route('/invitations/respond', name: 'invitation_respond', methods: ['GET'])]
    public function respond(Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->query->get('token');
        $response = $request->query->get('response');

        $invitation = $em->getRepository(Invitation::class)->findOneBy(['token' => $token]);

        if (!$invitation) {
            throw $this->createNotFoundException('Invitation non trouvée');
        }

        $invitation->setResponse($response);
        $em->flush();

        if ($response === 'yes') {
            return $this->redirectToRoute('');
        }

        return $this->redirectToRoute('');
    }
}
