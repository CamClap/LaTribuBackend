<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        // On récupère le payload actuel
        $payload = $event->getData();

        // Ajoute l'id utilisateur (si l'entité User a getId())
        $payload['id'] = $user->getId();

        // Remet à jour les données dans le token
        $event->setData($payload);
    }
}
