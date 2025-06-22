<?php
namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        // Récupérer le payload actuel
        $payload = $event->getData();

        // Ajouter l'id utilisateur
        $payload['id'] = $user->getId();

        // Ajouter le nom de l'utilisateur
        $payload['name'] = $user->getName();

        // Mettre à jour le payload dans le token
        $event->setData($payload);
    }
}
