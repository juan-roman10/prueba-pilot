<?php

namespace App\Services\User\Handlers\List;

use App\Services\User\Handlers\AbstractUserHandler;

class PaginateUsersHandler extends AbstractUserHandler
{
    public function handle(array $context): array
    {
        $query = $context['query'];
        $perPage = $context['filters']['per_page'] ?? 10;

        $context['users'] = $query->paginate($perPage);

        return parent::handle($context);
    }
}
