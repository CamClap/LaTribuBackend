<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Group;

class GroupProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private ProcessorInterface $processor
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
     error_log('GroupProcessor invoked');
        if ($data instanceof Group && !$data->getCreator()) {
            $data->setCreator($this->security->getUser());
            error_log('Creator set to user id: ' . $this->security->getUser()->getId());
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
