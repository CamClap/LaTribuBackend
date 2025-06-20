<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;
    private string $projectDir;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->projectDir = $params->get('kernel.project_dir');
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$data instanceof User) {
            return $data;
        }

        $request = $this->requestStack->getCurrentRequest();

        if ($request && $request->files->has('photo')) {
            $photoFile = $request->files->get('photo');

            $filename = uniqid() . '.' . $photoFile->guessExtension();
            $photoFile->move(
                $this->projectDir . '/public/uploads',
                $filename
            );

            $data->setPicture($filename);
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
