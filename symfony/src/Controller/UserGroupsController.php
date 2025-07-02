<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserGroupsController
{
    public function __construct(private GroupRepository $groupRepository)
    {
    }

    public function __invoke(Request $request, int $id): JsonResponse
    {
        $groups = $this->groupRepository->findGroupsByUserId($id);
        $data = [];
        foreach ($groups as $group) {
            $data[] = [
                'id' => $group->getId(),
                'name' => $group->getName(),
                'creator_id' => $group->getCreator()->getId(),
            ];
        }
        return new JsonResponse($data);
    }
}
