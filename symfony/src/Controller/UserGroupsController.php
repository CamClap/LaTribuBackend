<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserGroupsController
{
    private $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function __invoke(Request $request, int $id): JsonResponse
    {
        $groups = $this->groupRepository->findGroupsByUserId($id);

        $data = [];
        foreach ($groups as $group) {
            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
            ];
        }

        return new JsonResponse($data);
    }
}
