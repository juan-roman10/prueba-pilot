<?php

namespace App\Services\Category\Handlers\List;

use App\Services\Category\Handlers\AbstractCategoryHandler;

class PaginateCategoriesHandler extends AbstractCategoryHandler
{
    public function handle(array $context): array
    {
        $query = $context['query'];
        $perPage = $context['filters']['per_page'] ?? 10;

        $context['categories'] = $query->paginate($perPage);

        return parent::handle($context);
    }
}
